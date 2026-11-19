<?php

namespace App\Models;

use App\Enums\AttendanceStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_occurrence_id',
        'student_id',
        'status',
        'recorded_by',
    ];

    protected $casts = [
        'status' => AttendanceStatusEnum::class,
    ];

    public function sessionOccurrence(): BelongsTo
    {
        return $this->belongsTo(SessionOccurrence::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'recorded_by');
    }
}
