<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicYear extends Model
{
    protected $fillable = [
        'label',
        'start_date',
        'end_date',
        'is_current',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'is_current' => 'boolean',
    ];

    public function academicYearTerms(): HasMany
    {
        return $this->hasMany(AcademicYearTerm::class);
    }

    public function yearlyHolidays(): HasMany
    {
        return $this->hasMany(YearlyHoliday::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class);
    }

    public function scopeCurrent(Builder $query): Builder
    {
        return $query->where('is_current', true);
    }
}
