<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CanvasModule extends Model
{
    use HasFactory;

    protected $fillable = [
        'canvas_unit_id',
        'code',
        'name_ar',
        'name_fr',
        'position',
        'coefficient',
        'credits',
        'weekly_cm_minutes',
        'weekly_td_minutes',
        'weekly_tp_minutes',
        'weekly_atelier_minutes',
        'personal_work_minutes',
        'continuous_percent',
        'exam_percent',
    ];

    protected $casts = [
        'position'              => 'integer',
        'coefficient'           => 'integer',
        'credits'               => 'integer',
        'weekly_cm_minutes'     => 'integer',
        'weekly_td_minutes'     => 'integer',
        'weekly_tp_minutes'     => 'integer',
        'weekly_atelier_minutes'=> 'integer',
        'personal_work_minutes' => 'integer',
        'continuous_percent'    => 'integer',
        'exam_percent'          => 'integer',
    ];

    public function canvasUnit(): BelongsTo
    {
        return $this->belongsTo(CanvasUnit::class);
    }

    /** Derived helper — total weekly minutes. */
    public function getWeeklyTotalMinutesAttribute(): int
    {
        return $this->weekly_cm_minutes
             + $this->weekly_td_minutes
             + $this->weekly_tp_minutes
             + $this->weekly_atelier_minutes;
    }
}