<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wilaya extends Model
{
    protected $fillable = [
        'code',
        'name_ar',
        'name_fr',
    ];

    protected $casts = [
        'code' => 'integer',
    ];

    public function communes(): HasMany
    {
        return $this->hasMany(Commune::class);
    }

    public function teachers(): HasMany
    {
        return $this->hasMany(Teacher::class);
    }
}
