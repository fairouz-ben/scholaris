<?php

namespace App\Models;

use App\Enums\OccurrenceStatusEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SessionOccurrence extends Model
{
    use HasFactory;

    protected $fillable = [
        'schedule_id',
        'date',
        'status',
        'teacher_id',
        'room_id',
        'teacher_request_id',
    ];

    protected $casts = [
        'date'   => 'date',
        'status' => OccurrenceStatusEnum::class,
    ];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    /** Effective teacher for this date (differs from the schedule's teacher after a validated replacement). */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function teacherRequest(): BelongsTo
    {
        return $this->belongsTo(TeacherRequest::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function scopeHeld(Builder $query): Builder
    {
        return $query->where('status', OccurrenceStatusEnum::HELD->value);
    }
}
