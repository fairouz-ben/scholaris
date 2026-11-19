<?php

namespace App\Actions\TeacherRequests;

use App\Enums\OccurrenceStatusEnum;
use App\Enums\RequestStatusEnum;
use App\Enums\RequestTypeEnum;
use App\Models\SessionOccurrence;
use App\Models\TeacherRequest;
use App\Models\User;
use DomainException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ValidateTeacherRequest
{
    public function approve(TeacherRequest $request, User $validator): TeacherRequest
    {
        return DB::transaction(function () use ($request, $validator) {
            $request = $this->lockPending($request);

            $this->applyToTimetable($request);
            $this->close($request, RequestStatusEnum::VALIDATED, $validator);

            return $request;
        });
    }

    public function reject(TeacherRequest $request, User $validator): TeacherRequest
    {
        return DB::transaction(function () use ($request, $validator) {
            $request = $this->lockPending($request);

            $this->close($request, RequestStatusEnum::REJECTED, $validator);

            return $request;
        });
    }

    private function lockPending(TeacherRequest $request): TeacherRequest
    {
        $request = TeacherRequest::query()->lockForUpdate()->findOrFail($request->id);

        if (! $request->isPending()) {
            throw new DomainException('Only pending requests can be processed.');
        }

        return $request;
    }

    private function close(TeacherRequest $request, RequestStatusEnum $status, User $validator): void
    {
        $request->update([
            'status'       => $status,
            'validated_by' => $validator->id,
            'validated_at' => now(),
        ]);

        Log::info('teacher_request.' . $status->value, [
            'teacher_request_id' => $request->id,
            'type'               => $request->type->value,
            'validated_by'       => $validator->id,
        ]);
    }

    /**
     * Only a validated request may alter the timetable.
     * Makeup / supplementary requests carry no date or slot in the current schema,
     * so validating them does not touch occurrences yet.
     */
    private function applyToTimetable(TeacherRequest $request): void
    {
        if (! in_array($request->type, [RequestTypeEnum::ABSENCE, RequestTypeEnum::REPLACEMENT], true)) {
            return;
        }

        if ($request->session_occurrence_id === null) {
            throw new DomainException('This request is not linked to a session occurrence.');
        }

        $occurrence = SessionOccurrence::query()
            ->lockForUpdate()
            ->findOrFail($request->session_occurrence_id);

        if ($request->type === RequestTypeEnum::ABSENCE) {
            $occurrence->update([
                'status'             => OccurrenceStatusEnum::CANCELLED,
                'teacher_request_id' => $request->id,
            ]);

            return;
        }

        if ($request->replacement_teacher_id === null) {
            throw new DomainException('A replacement request needs a replacement teacher.');
        }

        $occurrence->update([
            'teacher_id'         => $request->replacement_teacher_id,
            'teacher_request_id' => $request->id,
        ]);
    }
}
