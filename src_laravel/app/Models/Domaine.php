<?php

namespace App\Models;

use App\Models\Department;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Domaine extends Model
{
   
    protected $fillable = [
        'department_id',
        'name_ar',
        'name_fr',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function filieres(): HasMany
    {
        return $this->hasMany(Filiere::class);
    }

    public function domaineLevels(): HasMany
    {
        return $this->hasMany(Domaine_level::class);
    }
    public function canvases(): HasMany
{
    return $this->hasMany(Canvas::class);
}
}
