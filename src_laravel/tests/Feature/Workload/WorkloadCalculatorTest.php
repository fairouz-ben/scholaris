<?php

namespace Tests\Feature\Workload;

use App\Actions\TeacherRequests\ValidateTeacherRequest;
use App\Enums\OccurrenceStatusEnum;
use App\Enums\RequestStatusEnum;
use App\Enums\RequestTypeEnum;
use App\Enums\WorkloadPeriodEnum;
use App\Models\AbsenceType;
use App\Models\Holiday;
use App\Models\Teacher;
use App\Models\TeacherRequest;
use App\Models\User;
use App\Models\Workload;
use App\Models\YearlyHoliday;
use App\Services\Workload\WorkloadCalculator;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Concerns\CreatesTimetableFixtures;
use Tests\TestCase;

class WorkloadCalculatorTest extends TestCase
{
    use CreatesTimetableFixtures, RefreshDatabase;

    private function term()
    {
        return $this->timetableContext()['yearTerm'];
    }

    private function forTerm(Teacher $teacher): Workload
    {
        return app(WorkloadCalculator::class)->calculateForTerm($teacher, $this->term());
    }

    private function forPeriod(Teacher $teacher, int $weeks): Workload
    {
        return app(WorkloadCalculator::class)->calculate(
            $teacher,
            $this->term(),
            WorkloadPeriodEnum::MONTH,
            Carbon::parse('2026-10-01'),
            Carbon::parse('2026-10-31'),
            $weeks,
        );
    }

    /** Creates $count held CM sessions of 90 minutes on consecutive days, skipping Fridays. */
    private function holdCmSessions(Teacher $teacher, int $count): void
    {
        $schedule = $this->makeSchedule($teacher, 'cm');
        $day = Carbon::parse('2026-10-01');
        $created = 0;

        while ($created < $count) {
            if ($day->dayOfWeekIso !== 5) {
                $this->makeOccurrence($teacher, $day->toDateString(), OccurrenceStatusEnum::HELD, $schedule);
                $created++;
            }
            $day = $day->addDay();
        }
    }

    private function absenceType(int $percent): AbsenceType
    {
        return AbsenceType::create([
            'code'              => "abs{$percent}",
            'name_ar'           => 'غياب',
            'name_fr'           => 'Absence',
            'deduction_percent' => $percent,
        ]);
    }

    private function validateAbsence($occurrence, ?AbsenceType $type): void
    {
        $request = TeacherRequest::create([
            'teacher_id'            => $occurrence->teacher_id,
            'type'                  => RequestTypeEnum::ABSENCE,
            'status'                => RequestStatusEnum::PENDING,
            'absence_type_id'       => $type?->id,
            'session_occurrence_id' => $occurrence->id,
        ]);

        app(ValidateTeacherRequest::class)->approve($request, User::factory()->create());
    }

    public function test_session_types_are_converted_to_equivalent_cours(): void
    {
        $teacher = $this->makeTeacher();

        $this->makeOccurrence($teacher, '2026-10-05', OccurrenceStatusEnum::HELD, $this->makeSchedule($teacher, 'cm'));
        $this->makeOccurrence($teacher, '2026-10-06', OccurrenceStatusEnum::HELD, $this->makeSchedule($teacher, 'td'));
        $this->makeOccurrence($teacher, '2026-10-07', OccurrenceStatusEnum::HELD, $this->makeSchedule($teacher, 'tp'));

        $workload = $this->forTerm($teacher);

        $this->assertSame(270, $workload->raw_minutes);
        $this->assertEqualsWithDelta(195.0, (float) $workload->equivalent_minutes, 0.01);
        $this->assertEqualsWithDelta(195.0, (float) $workload->paid_minutes, 0.01);
    }

    public function test_term_uses_teaching_weeks_for_obligation_and_cap(): void
    {
        $workload = $this->forTerm($this->makeTeacher());

        $this->assertSame(12, $workload->weeks);
        $this->assertSame(8 * 60 * 12, $workload->obligation_minutes);
        $this->assertSame(20 * 60 * 12, $workload->cap_minutes);
    }

    public function test_planned_and_unrequested_cancelled_sessions_are_not_counted(): void
    {
        $teacher = $this->makeTeacher();
        $schedule = $this->makeSchedule($teacher);

        $this->makeOccurrence($teacher, '2026-10-05', OccurrenceStatusEnum::PLANNED, $schedule);
        $this->makeOccurrence($teacher, '2026-10-06', OccurrenceStatusEnum::CANCELLED, $schedule);

        $this->assertSame(0, $this->forTerm($teacher)->raw_minutes);
    }

    public function test_fridays_and_holidays_are_excluded(): void
    {
        $teacher = $this->makeTeacher();
        $schedule = $this->makeSchedule($teacher);

        $holiday = Holiday::create([
            'name_ar'  => 'عطلة',
            'name_fr'  => 'Jour férié',
            'is_fixed' => false,
            'category' => 'national',
        ]);

        YearlyHoliday::create([
            'academic_year_id'      => $this->timetableContext()['year']->id,
            'academic_year_term_id' => $this->term()->id,
            'holiday_id'            => $holiday->id,
            'start_date'            => '2026-10-12',
            'end_date'              => '2026-10-12',
        ]);

        $this->makeOccurrence($teacher, '2026-10-05', OccurrenceStatusEnum::HELD, $schedule); // Monday
        $this->makeOccurrence($teacher, '2026-10-09', OccurrenceStatusEnum::HELD, $schedule); // Friday
        $this->makeOccurrence($teacher, '2026-10-12', OccurrenceStatusEnum::HELD, $schedule); // holiday

        $this->assertSame(90, $this->forTerm($teacher)->raw_minutes);
    }

    public function test_validated_absence_is_deducted_in_full_when_type_is_100_percent(): void
    {
        $teacher = $this->makeTeacher();
        $schedule = $this->makeSchedule($teacher);

        $this->makeOccurrence($teacher, '2026-10-05', OccurrenceStatusEnum::HELD, $schedule);
        $absent = $this->makeOccurrence($teacher, '2026-10-06', OccurrenceStatusEnum::PLANNED, $schedule);
        $this->validateAbsence($absent, $this->absenceType(100));

        $workload = $this->forTerm($teacher);

        $this->assertSame(180, $workload->raw_minutes);
        $this->assertEqualsWithDelta(180.0, (float) $workload->equivalent_minutes, 0.01);
        $this->assertEqualsWithDelta(90.0, (float) $workload->absence_deduction_minutes, 0.01);
        $this->assertEqualsWithDelta(90.0, (float) $workload->net_minutes, 0.01);
    }

    public function test_justified_absence_with_zero_percent_deducts_nothing(): void
    {
        $teacher = $this->makeTeacher();
        $schedule = $this->makeSchedule($teacher);

        $this->makeOccurrence($teacher, '2026-10-05', OccurrenceStatusEnum::HELD, $schedule);
        $absent = $this->makeOccurrence($teacher, '2026-10-06', OccurrenceStatusEnum::PLANNED, $schedule);
        $this->validateAbsence($absent, $this->absenceType(0));

        $workload = $this->forTerm($teacher);

        $this->assertEqualsWithDelta(0.0, (float) $workload->absence_deduction_minutes, 0.01);
        $this->assertEqualsWithDelta(180.0, (float) $workload->net_minutes, 0.01);
    }

    public function test_absence_without_type_is_fully_deducted(): void
    {
        $teacher = $this->makeTeacher();
        $absent = $this->makeOccurrence($teacher, '2026-10-06');
        $this->validateAbsence($absent, null);

        $workload = $this->forTerm($teacher);

        $this->assertEqualsWithDelta(90.0, (float) $workload->absence_deduction_minutes, 0.01);
        $this->assertEqualsWithDelta(0.0, (float) $workload->net_minutes, 0.01);
    }

    public function test_validated_replacement_moves_the_hours_to_the_replacement_teacher(): void
    {
        $original = $this->makeTeacher();
        $replacement = $this->makeTeacher();
        $occurrence = $this->makeOccurrence($original, '2026-10-05');

        $request = TeacherRequest::create([
            'teacher_id'             => $original->id,
            'type'                   => RequestTypeEnum::REPLACEMENT,
            'status'                 => RequestStatusEnum::PENDING,
            'session_occurrence_id'  => $occurrence->id,
            'replacement_teacher_id' => $replacement->id,
        ]);
        app(ValidateTeacherRequest::class)->approve($request, User::factory()->create());

        $occurrence->refresh()->update(['status' => OccurrenceStatusEnum::HELD]);

        $this->assertSame(0, $this->forTerm($original)->raw_minutes);
        $this->assertSame(90, $this->forTerm($replacement)->raw_minutes);
    }

    #[DataProvider('capacityCases')]
    public function test_paid_overtime_excess_and_status(
        string $contract,
        int $sessions,
        int $paid,
        int $overtime,
        int $excess,
        string $status,
    ): void {
        $teacher = $this->makeTeacher($contract);
        $this->holdCmSessions($teacher, $sessions);

        $workload = $this->forPeriod($teacher, weeks: 1);

        $this->assertEqualsWithDelta($paid, (float) $workload->paid_minutes, 0.01);
        $this->assertEqualsWithDelta($overtime, (float) $workload->overtime_minutes, 0.01);
        $this->assertEqualsWithDelta($excess, (float) $workload->excess_minutes, 0.01);
        $this->assertSame($status, $workload->status->value);
    }

    /** 1 week: permanent obligation 480 / cap 1200, vacataire cap 720 (minutes, 90 per CM session). */
    public static function capacityCases(): array
    {
        return [
            'permanent under obligation'  => ['permanent', 3, 270, 0, 0, 'normal'],
            'permanent with overtime'     => ['permanent', 7, 630, 150, 0, 'overload'],
            'permanent above the cap'     => ['permanent', 15, 1200, 720, 150, 'critical'],
            'vacataire under the cap'     => ['vacataire', 5, 450, 0, 0, 'normal'],
            'vacataire above the cap'     => ['vacataire', 15, 720, 0, 630, 'critical'],
        ];
    }

    public function test_recalculation_refreshes_the_same_snapshot(): void
    {
        $teacher = $this->makeTeacher();
        $schedule = $this->makeSchedule($teacher);
        $this->makeOccurrence($teacher, '2026-10-05', OccurrenceStatusEnum::HELD, $schedule);

        $first = $this->forTerm($teacher);

        $this->makeOccurrence($teacher, '2026-10-06', OccurrenceStatusEnum::HELD, $schedule);
        $second = $this->forTerm($teacher);

        $this->assertSame($first->id, $second->id);
        $this->assertSame(1, Workload::count());
        $this->assertSame(180, $second->raw_minutes);
    }

    public function test_period_needs_at_least_one_week(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->forPeriod($this->makeTeacher(), weeks: 0);
    }
}
