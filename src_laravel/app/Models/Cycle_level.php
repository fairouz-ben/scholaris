<?php

namespace App\Models;

use App\Enums\EntityStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cycle_level extends Model
{
   
    protected $fillable = [
        'cycle_id',
        'name_ar',
        'name_fr',
        'code_ar',
        'code_fr',
        'status',
    ];

    protected $casts = [
        'status' => EntityStatusEnum::class,
    ];

    public function cycle(): BelongsTo
    {
        return $this->belongsTo(Cycle::class);
    }

    public function domaineLevels(): HasMany
    {
        return $this->hasMany(Domaine_level::class);
    }

    public function specialityLevels(): HasMany
    {
        return $this->hasMany(Speciality_level::class);
    }
    public function canvases(): HasMany
{
    return $this->hasMany(Canvas::class);
}
}
