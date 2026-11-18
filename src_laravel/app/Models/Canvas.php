<?php

namespace App\Models;

use App\Enums\CanvasKindEnum;
use App\Enums\EntityStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Canvas extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Table name is singular in the schema ("canvas"), so it must be
     * declared explicitly (Laravel's default would also yield "canvas",
     * but explicit is safer against inflector changes).
     */
    protected $table = 'canvas';

    protected $fillable = [
        'reference',
        'kind',
        'parent_canvas_id',
        'name_ar',
        'name_fr',
        'canvas_year',
        'cycle_level_id',
        'speciality_id',
        'domaine_id',
        'total_weeks',
        'status',
    ];

    protected $casts = [
        'kind'        => CanvasKindEnum::class,
        'status'      => EntityStatusEnum::class,
        'total_weeks' => 'integer',
    ];

    // ---- Self-referencing (amendment → base) ----

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Canvas::class, 'parent_canvas_id');
    }

    public function amendments(): HasMany
    {
        return $this->hasMany(Canvas::class, 'parent_canvas_id');
    }

    // ---- Scope ----

    public function cycleLevel(): BelongsTo
    {
        return $this->belongsTo(Cycle_level::class);
    }

    public function speciality(): BelongsTo
    {
        return $this->belongsTo(Speciality::class);
    }

    public function domaine(): BelongsTo
    {
        return $this->belongsTo(Domaine::class);
    }

    // ---- Children ----

    public function canvasTerms(): HasMany
    {
        return $this->hasMany(CanvasTerm::class);
    }
}