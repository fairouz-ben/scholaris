<?php

namespace App\Models;

use App\Enums\TermTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Term extends Model
{
    use HasFactory;

    protected $fillable = [
        'term_type',
        'name_ar',
        'name_fr',
        'term_number',
        'code',
    ];

    protected $casts = [
        'term_type'   => TermTypeEnum::class,
        'term_number' => 'integer',
    ];

    // ------------------------------------------------------------------
    // Relations
    // ------------------------------------------------------------------

    /**
     * Academic-year instances of this term
     * (e.g. "Semestre 1 · 2026-2027").
     */
    public function academicYearTerms(): HasMany
    {
        return $this->hasMany(AcademicYearTerm::class);
    }

    /**
     * Canvas ↔ term pivot rows (a canvas can span several terms).
     */
    public function canvasTerms(): HasMany
    {
        return $this->hasMany(CanvasTerm::class);
    }

    /**
     * Convenience shortcut: canvases linked to this term via the pivot.
     */
    public function canvases()
    {
        return $this->belongsToMany(
            Canvas::class,
            'canvas_terms',
            'term_id',
            'canvas_id'
        )->withTimestamps();
    }
}