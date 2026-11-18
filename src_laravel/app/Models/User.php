<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\LanguageEnum;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * Admin-only accounts (web guard).
     * Teachers authenticate through Teacher via the `teacher` guard.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'locale',
        'faculty_id',
        'department_id',
        'is_active',
        'teacher_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'locale'      => LanguageEnum::class,
            'is_active'   => 'boolean',
            'email_verified_at' => 'datetime',

            'password' => 'hashed',
        ];
    }
    // Relations --------------------------------------------------------

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    // Convenience ------------------------------------------------------

    public function isSuperAdmin(): bool
    {
        return $this->faculty_id === null
            && $this->department_id === null
            && $this->hasRole('super_admin');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
