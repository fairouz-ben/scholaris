<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Grade extends Model
{
    
    protected $fillable = [
        'name_ar',
        'name_fr',
        'code_ar',
        'code_fr',
        'priority',
        'can_teach_cm',
        'priority_for_uef',
    ];

    protected $casts = [
        'priority'         => 'integer',
        'can_teach_cm'     => 'boolean',
        'priority_for_uef' => 'boolean',
    ];

    public function teachers(): HasMany
    {
        return $this->hasMany(Teacher::class);
    }
}
