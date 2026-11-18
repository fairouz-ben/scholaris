<?php

namespace App\Models;

use App\Models\Domaine;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Filiere extends Model
{
    
    protected $fillable = [
        'domaine_id',
        'name_ar',
        'name_fr',
    ];

    public function domaine(): BelongsTo
    {
        return $this->belongsTo(Domaine::class);
    }

    public function specialites(): HasMany
    {
        return $this->hasMany(Speciality::class);
    }
}
