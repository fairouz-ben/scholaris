<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SessionType extends Model
{
    protected $fillable = [
        'code',
        'name_ar',
        'name_fr',
        'hours_per_cm_hour',
    ];

    protected $casts = [
        'hours_per_cm_hour' => 'decimal:2',
    ];

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }
}
