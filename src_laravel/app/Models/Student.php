<?php

namespace App\Models;

use App\Enums\StudentStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Student extends Authenticatable
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'matricule',
        'email',
        'password',
        'last_name_ar',
        'first_name_ar',
        'last_name_fr',
        'first_name_fr',
        'birth_date',
        'birth_place',
        'specialite_id',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'status'     => StudentStatusEnum::class,
        'password'   => 'hashed',
    ];

    public function specialite(): BelongsTo
    {
        return $this->belongsTo(Speciality::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Groups where this student is the class representative.
     */
    public function representedGroups(): HasMany
    {
        return $this->hasMany(SectionGroup::class, 'representative_student_id');
    }
}