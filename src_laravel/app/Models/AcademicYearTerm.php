<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicYearTerm extends Model
{
    protected $fillable = [
        'academic_year_id',
        'term_id',
        'start_date',
        'end_date',
        'teaching_weeks',
        'is_current',
    ];

    protected $casts = [
        'start_date'     => 'date',
        'end_date'       => 'date',
        'teaching_weeks' => 'integer',
        'is_current'     => 'boolean',
    ];

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }

    public function calendarEvents(): HasMany
    {
        return $this->hasMany(AcademicCalendarEvent::class);
    }

    public function yearlyHolidays(): HasMany
    {
        return $this->hasMany(YearlyHoliday::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    public function scopeCurrent(Builder $query): Builder
    {
        return $query->where('is_current', true);
    }
}
