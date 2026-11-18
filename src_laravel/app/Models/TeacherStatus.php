<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TeacherStatus extends Model
{
    protected  $table = 'teacher_status';

     protected $fillable = [
        'is_active',
        'name_ar',
        'name_fr',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function teachers(): HasMany
    {
        return $this->hasMany(Teacher::class);
    }
}
