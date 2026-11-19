<?php

namespace App\Models;

use App\Enums\RequestStatusEnum;
use App\Enums\RequestTypeEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TeacherRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'teacher_id',
        'type',
        'status',
        'absence_type_id',
        'session_occurrence_id',
        'replacement_teacher_id',
        'reason',
        'validated_by',
        'validated_at',
    ];

    protected $casts = [
        'type'         => RequestTypeEnum::class,
        'status'       => RequestStatusEnum::class,
        'validated_at' => 'datetime',
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function replacementTeacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'replacement_teacher_id');
    }

    public function absenceType(): BelongsTo
    {
        return $this->belongsTo(AbsenceType::class);
    }

    public function sessionOccurrence(): BelongsTo
    {
        return $this->belongsTo(SessionOccurrence::class);
    }

    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function isPending(): bool
    {
        return $this->status === RequestStatusEnum::PENDING;
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', RequestStatusEnum::PENDING->value);
    }
}
