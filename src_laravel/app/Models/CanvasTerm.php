<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CanvasTerm extends Model
{
    use HasFactory;

    protected $fillable = [
        'canvas_id',
        'term_id',
    ];

    public function canvas(): BelongsTo
    {
        return $this->belongsTo(Canvas::class);
    }

    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }

    public function canvasUnits(): HasMany
    {
        return $this->hasMany(CanvasUnit::class);
    }
}