<?php

namespace App\Models;

use App\Enums\EntityStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Speciality_level extends Model
{
    protected $fillable = [
        'speciality_id',
        'cycle_level_id',
        'status',
    ];

    protected $casts = [
        'status' => EntityStatusEnum::class,
    ];

    public function speciality(): BelongsTo
    {
        return $this->belongsTo(Speciality::class);
    }

    public function cycleLevel(): BelongsTo
    {
        return $this->belongsTo(Cycle_level::class);
    }
}
