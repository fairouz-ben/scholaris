<?php

namespace Tests\Feature\TeacherRequests;

use App\Actions\TeacherRequests\ValidateTeacherRequest;
use App\Enums\AttendanceStatusEnum;
use App\Enums\OccurrenceStatusEnum;
use App\Enums\RequestStatusEnum;
use App\Enums\RequestTypeEnum;
use App\Models\Attendance;
use App\Models\SessionOccurrence;
use App\Models\TeacherRequest;
use App\Models\User;
use DomainException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesTimetableFixtures;
use Tests\TestCase;

class ValidateTeacherRequestTest extends TestCase
{
    use CreatesTimetableFixtures, RefreshDatabase;

    private function makeRequest(SessionOccurrence $occurrence, RequestTypeEnum $type, ?int $replacementId = null): TeacherRequest
    {
        return TeacherRequest::create([
            'teacher_id'             => $occurrence->teacher_id,
            'type'                   => $type,
            'status'                 => RequestStatusEnum::PENDING,
            'session_occurrence_id'  => $occurrence->id,
            'replacement_teacher_id' => $replacementId,
            'reason'                 => 'Conference',
        ]);
    }

    public function test_pending_request_does_not_alter_the_timetable(): void
    {
        $occurrence = $this->makeOccurrence();
        $replacement = $this->makeTeacher();
        $request = $this->makeRequest($occurrence, RequestTypeEnum::REPLACEMENT, $replacement->id);

        $occurrence->refresh();

        $this->assertTrue($request->fresh()->isPending());
        $this->assertNotSame($replacement->id, $occurrence->teacher_id);
        $this->assertSame(OccurrenceStatusEnum::PLANNED, $occurrence->status);
    }

    public function test_validated_replacement_swaps_the_effective_teacher(): void
    {
        $occurrence = $this->makeOccurrence();
        $replacement = $this->makeTeacher();
        $request = $this->makeRequest($occurrence, RequestTypeEnum::REPLACEMENT, $replacement->id);
        $admin = User::factory()->create();

        app(ValidateTeacherRequest::class)->approve($request, $admin);

        $occurrence->refresh();
        $request->refresh();

        $this->assertSame($replacement->id, $occurrence->teacher_id);
        $this->assertSame($request->id, $occurrence->teacher_request_id);
        $this->assertSame(RequestStatusEnum::VALIDATED, $request->status);
        $this->assertSame($admin->id, $request->validated_by);
        $this->assertNotNull($request->validated_at);
    }

    public function test_validated_absence_cancels_the_occurrence(): void
    {
        $occurrence = $this->makeOccurrence();
        $request = $this->makeRequest($occurrence, RequestTypeEnum::ABSENCE);

        app(ValidateTeacherRequest::class)->approve($request, User::factory()->create());

        $this->assertSame(OccurrenceStatusEnum::CANCELLED, $occurrence->fresh()->status);
    }

    public function test_rejected_request_leaves_the_timetable_untouched(): void
    {
        $occurrence = $this->makeOccurrence();
        $originalTeacherId = $occurrence->teacher_id;
        $request = $this->makeRequest($occurrence, RequestTypeEnum::ABSENCE);

        app(ValidateTeacherRequest::class)->reject($request, User::factory()->create());

        $occurrence->refresh();

        $this->assertSame(RequestStatusEnum::REJECTED, $request->fresh()->status);
        $this->assertSame(OccurrenceStatusEnum::PLANNED, $occurrence->status);
        $this->assertSame($originalTeacherId, $occurrence->teacher_id);
    }

    public function test_a_processed_request_cannot_be_processed_again(): void
    {
        $request = $this->makeRequest($this->makeOccurrence(), RequestTypeEnum::ABSENCE);
        $admin = User::factory()->create();
        $action = app(ValidateTeacherRequest::class);

        $action->reject($request, $admin);

        $this->expectException(DomainException::class);
        $action->approve($request, $admin);
    }

    public function test_replacement_without_a_replacement_teacher_rolls_back(): void
    {
        $occurrence = $this->makeOccurrence();
        $request = $this->makeRequest($occurrence, RequestTypeEnum::REPLACEMENT);

        try {
            app(ValidateTeacherRequest::class)->approve($request, User::factory()->create());
            $this->fail('A DomainException was expected.');
        } catch (DomainException) {
            $this->assertTrue($request->fresh()->isPending());
            $this->assertNull($occurrence->fresh()->teacher_request_id);
        }
    }

    public function test_attendance_is_unique_per_occurrence_and_student(): void
    {
        $occurrence = $this->makeOccurrence();
        $student = $this->makeStudent();

        $data = [
            'session_occurrence_id' => $occurrence->id,
            'student_id'            => $student->id,
            'status'                => AttendanceStatusEnum::PRESENT,
            'recorded_by'           => $occurrence->teacher_id,
        ];

        Attendance::create($data);

        $this->expectException(QueryException::class);
        Attendance::create($data);
    }
}
