<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Speciality extends Model
{
     protected $fillable = [
        'filiere_id',
        'name_ar',
        'name_fr',
    ];

    public function filiere(): BelongsTo
    {
        return $this->belongsTo(Filiere::class);
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
