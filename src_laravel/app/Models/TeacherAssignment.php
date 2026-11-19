<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherAssignment extends Model
{
    protected $fillable = [
        'teacher_id',
        'academic_year_id',
        'canvas_module_id',
        'section_id',
        'session_type_id',
        'is_temporary',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'is_temporary' => 'boolean',
        'start_date'   => 'date',
        'end_date'     => 'date',
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function canvasModule(): BelongsTo
    {
        return $this->belongsTo(CanvasModule::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function sessionType(): BelongsTo
    {
        return $this->belongsTo(SessionType::class);
    }
}
