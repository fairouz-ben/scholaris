<?php

namespace App\Models;

use App\Enums\CivilityEnum;
use App\Enums\ContractTypeEnum;
use App\Enums\MaritalStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Teacher extends Authenticatable
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'department_id',
        'last_name_ar',
        'first_name_ar',
        'last_name_fr',
        'first_name_fr',
        'birth_date',
        'birth_place',
        'civility',
        'marital_status',
        'children_count',
        'phone',
        'email',
        'password',
        'address',
        'postal_code',
        'wilaya_id',
        'teacher_status_id',
        'grade_id',
        'contract_type',
        'diploma',
        'diploma_domaine',
        'diploma_filiere',
        'diploma_specialite',
        'hire_date',
        'national_id',
        'ccp_rib',
        'social_security_number',
        'nationality',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'birth_date'     => 'date',
        'hire_date'      => 'date',
        'civility'       => CivilityEnum::class,
        'marital_status' => MaritalStatusEnum::class,
        'contract_type'  => ContractTypeEnum::class,
        'children_count' => 'integer',
        'password'       => 'hashed',
    ];

    // ---- Org ----

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function wilaya(): BelongsTo
    {
        return $this->belongsTo(Wilaya::class);
    }

    public function teacherStatus(): BelongsTo
    {
        return $this->belongsTo(TeacherStatus::class);
    }

    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class);
    }

    // ---- Teaching ----

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class);
    }
}