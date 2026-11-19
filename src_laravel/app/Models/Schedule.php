<?php

namespace App\Models;

use App\Enums\WeekPatternEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Schedule extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'academic_year_term_id',
        'canvas_module_id',
        'section_id',
        'section_group_id',
        'teacher_id',
        'room_id',
        'session_type_id',
        'session_category_id',
        'day_of_week',
        'time_slot_id',
        'week_pattern',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'day_of_week'  => 'integer',
        'week_pattern' => WeekPatternEnum::class,
        'start_date'   => 'date',
        'end_date'     => 'date',
    ];

    public function academicYearTerm(): BelongsTo
    {
        return $this->belongsTo(AcademicYearTerm::class);
    }

    public function canvasModule(): BelongsTo
    {
        return $this->belongsTo(CanvasModule::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function sectionGroup(): BelongsTo
    {
        return $this->belongsTo(SectionGroup::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function sessionType(): BelongsTo
    {
        return $this->belongsTo(SessionType::class);
    }

    public function sessionCategory(): BelongsTo
    {
        return $this->belongsTo(SessionCategory::class);
    }

    public function timeSlot(): BelongsTo
    {
        return $this->belongsTo(TimeSlot::class);
    }

    public function occurrences(): HasMany
    {
        return $this->hasMany(SessionOccurrence::class);
    }
}
