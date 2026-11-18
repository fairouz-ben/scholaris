<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SectionGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'section_id',
        'name',
        'position',
        'capacity',
        'representative_student_id',
    ];

    protected $casts = [
        'position' => 'integer',
        'capacity' => 'integer',
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function representativeStudent(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'representative_student_id');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }
}