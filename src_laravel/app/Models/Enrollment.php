<?php

namespace App\Models;


use App\Enums\EnrollmentStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'academic_year_id',
        'cycle_level_id',
        'section_id',
        'section_group_id',
        'status',
    ];

    protected $casts = [
        'status' => EnrollmentStatusEnum::class,
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function cycleLevel(): BelongsTo
    {
        return $this->belongsTo(Cycle_level::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function sectionGroup(): BelongsTo
    {
        return $this->belongsTo(SectionGroup::class);
    }
}