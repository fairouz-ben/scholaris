<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AbsenceType extends Model
{
    protected $fillable = [
        'code',
        'name_ar',
        'name_fr',
        'deduction_percent',
    ];

    protected $casts = [
        'deduction_percent' => 'integer',
    ];

    public function teacherRequests(): HasMany
    {
        return $this->hasMany(TeacherRequest::class);
    }
}
