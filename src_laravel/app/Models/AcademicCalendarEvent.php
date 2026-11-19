<?php

namespace App\Models;

use App\Enums\CalendarEventTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AcademicCalendarEvent extends Model
{
    protected $fillable = [
        'academic_year_term_id',
        'type',
        'name_ar',
        'name_fr',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'type'       => CalendarEventTypeEnum::class,
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    public function academicYearTerm(): BelongsTo
    {
        return $this->belongsTo(AcademicYearTerm::class);
    }
}
