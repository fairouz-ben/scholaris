<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{

    protected $fillable = [
        'faculty_id',
        'name_ar',
        'name_fr',
        'head_teacher_id',
        'signatory_title_ar',
        'signatory_title_fr',
    ];


    public function teachers(): HasMany
    {
        return $this->hasMany(Teacher::class);
    }
    public function headTeacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'head_teacher_id');
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }
}
