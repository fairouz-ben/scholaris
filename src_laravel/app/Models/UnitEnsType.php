<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UnitEnsType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_ar',
        'name_fr',
        'code_ar',
        'code_fr',
    ];

    public function canvasUnits(): HasMany
    {
        return $this->hasMany(CanvasUnit::class);
    }
}