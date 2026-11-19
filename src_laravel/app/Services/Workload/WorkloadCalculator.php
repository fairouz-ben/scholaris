<?php

namespace App\Services\Workload;

use App\Enums\ContractTypeEnum;
use App\Enums\OccurrenceStatusEnum;
use App\Enums\RequestStatusEnum;
use App\Enums\RequestTypeEnum;
use App\Enums\WorkloadPeriodEnum;
use App\Enums\WorkloadStatusEnum;
use App\Models\AcademicYearTerm;
use App\Models\SessionOccurrence;
use App\Models\Teacher;
use App\Models\Workload;
use App\Models\YearlyHoliday;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class WorkloadCalculator
{
    public function calculateForTerm(Teacher $teacher, AcademicYearTerm $term): Workload
    {
        return $this->calculate(
            $teacher,
            $term,
            WorkloadPeriodEnum::TERM,
            $term->start_date,
            $term->end_date,
            (int) $term->teaching_weeks,
        );
    }

    /**
     * Builds (or refreshes) the workload snapshot of a teacher for a period.
     * $weeks drives the obligation and the cap, so the caller states how many teaching weeks the period holds.
     */
    public function calculate(
        Teacher $teacher,
        AcademicYearTerm $term,
        WorkloadPeriodEnum $period,
        CarbonInterface $start,
        CarbonInterface $end,
        int $weeks,
    ): Workload {
        if ($weeks < 1) {
            throw new InvalidArgumentException('A workload period needs at least one teaching week.');
        }

        if ($end->lt($start)) {
            throw new InvalidArgumentException('The period end must not precede its start.');
        }

        [$rawMinutes, $gross, $deduction] = $this->sumOccurrences($teacher, $term, $start, $end);

        $net = max($gross - $deduction, 0.0);

        $isPermanent = $teacher->contract_type === ContractTypeEnum::PERMANENT;
        $obligation = $isPermanent ? (int) config('workload.weekly_obligation_hours') * 60 * $weeks : 0;
        $cap = (int) config('workload.weekly_max_hours.' . $teacher->contract_type->value) * 60 * $weeks;

        $paid = min($net, $cap);
        $overtime = $isPermanent ? max($paid - $obligation, 0.0) : 0.0;
        $excess = $net - $paid;

        return Workload::updateOrCreate(
            [
                'teacher_id'            => $teacher->id,
                'academic_year_term_id' => $term->id,
                'period_type'           => $period->value,
                'period_start'          => $start->toDateString(),
            ],
            [
                'period_end'                => $end->toDateString(),
                'weeks'                     => $weeks,
                'raw_minutes'               => $rawMinutes,
                'equivalent_minutes'        => round($gross, 2),
                'absence_deduction_minutes' => round($deduction, 2),
                'net_minutes'               => round($net, 2),
                'obligation_minutes'        => $obligation,
                'cap_minutes'               => $cap,
                'paid_minutes'              => round($paid, 2),
                'overtime_minutes'          => round($overtime, 2),
                'excess_minutes'            => round($excess, 2),
                'status'                    => $this->status($isPermanent, $net, $obligation, $cap),
                'calculated_at'             => now(),
            ],
        );
    }

    private function status(bool $isPermanent, float $net, int $obligation, int $cap): WorkloadStatusEnum
    {
        if ($net > $cap) {
            return WorkloadStatusEnum::CRITICAL;
        }

        if ($isPermanent && $net > $obligation) {
            return WorkloadStatusEnum::OVERLOAD;
        }

        return WorkloadStatusEnum::NORMAL;
    }

    /**
     * Counts held sessions, plus sessions cancelled by a validated absence of the teacher.
     * The latter enter the gross total and are then deducted according to the absence type.
     *
     * @return array{0: int, 1: float, 2: float} raw minutes, gross équivalent-cours minutes, deduction minutes
     */
    private function sumOccurrences(Teacher $teacher, AcademicYearTerm $term, CarbonInterface $start, CarbonInterface $end): array
    {
        $holidays = $this->holidays($term, $start, $end);
        $excludedDays = (array) config('workload.excluded_iso_weekdays');

        $occurrences = SessionOccurrence::query()
            ->with([
                'schedule' => fn ($query) => $query->withTrashed()->with(['timeSlot', 'sessionType']),
                'teacherRequest.absenceType',
            ])
            ->where('teacher_id', $teacher->id)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->whereIn('status', [OccurrenceStatusEnum::HELD->value, OccurrenceStatusEnum::CANCELLED->value])
            ->get();

        $raw = 0;
        $gross = 0.0;
        $deduction = 0.0;

        foreach ($occurrences as $occurrence) {
            if (in_array($occurrence->date->dayOfWeekIso, $excludedDays, true)) {
                continue;
            }

            if ($holidays->contains(fn (YearlyHoliday $h) => $occurrence->date->between($h->start_date, $h->end_date))) {
                continue;
            }

            $isAbsence = $occurrence->status === OccurrenceStatusEnum::CANCELLED;

            if ($isAbsence && ! $this->isValidatedAbsence($occurrence)) {
                continue;
            }

            $minutes = $this->durationMinutes($occurrence);
            $equivalent = $this->toEquivalent($occurrence, $minutes);

            $raw += $minutes;
            $gross += $equivalent;

            if ($isAbsence) {
                // An absence without a type is treated as fully deducted.
                $percent = $occurrence->teacherRequest->absenceType?->deduction_percent ?? 100;
                $deduction += $equivalent * $percent / 100;
            }
        }

        return [$raw, $gross, $deduction];
    }

    private function isValidatedAbsence(SessionOccurrence $occurrence): bool
    {
        $request = $occurrence->teacherRequest;

        return $request !== null
            && $request->type === RequestTypeEnum::ABSENCE
            && $request->status === RequestStatusEnum::VALIDATED;
    }

    private function durationMinutes(SessionOccurrence $occurrence): int
    {
        $slot = $occurrence->schedule->timeSlot;

        return (int) round(abs(Carbon::parse($slot->start_time)->diffInMinutes(Carbon::parse($slot->end_time))));
    }

    /** session_types.hours_per_cm_hour is the number of hours of that type that equal one hour of cours. */
    private function toEquivalent(SessionOccurrence $occurrence, int $minutes): float
    {
        $ratio = (float) $occurrence->schedule->sessionType->hours_per_cm_hour;

        return $ratio > 0 ? $minutes / $ratio : 0.0;
    }

    /** @return Collection<int, YearlyHoliday> */
    private function holidays(AcademicYearTerm $term, CarbonInterface $start, CarbonInterface $end): Collection
    {
        return YearlyHoliday::query()
            ->where('academic_year_id', $term->academic_year_id)
            ->whereDate('start_date', '<=', $end->toDateString())
            ->whereDate('end_date', '>=', $start->toDateString())
            ->get();
    }
}
