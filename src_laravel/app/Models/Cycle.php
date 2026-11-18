<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cycle extends Model
{
     protected $fillable = [
        'name_ar',
        'name_fr',
        'code',
        'info',
    ];

    public function cycleLevels(): HasMany
    {
        return $this->hasMany(Cycle_level::class);
    }
}
