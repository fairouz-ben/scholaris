<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CanvasUnit extends Model
{
    use HasFactory;

    protected $fillable = [
        'canvas_term_id',
        'unit_ens_type_id',
        'code',
        'position',
        'coefficient',
        'credits',
    ];

    protected $casts = [
        'position'    => 'integer',
        'coefficient' => 'integer',
        'credits'     => 'integer',
    ];

    public function canvasTerm(): BelongsTo
    {
        return $this->belongsTo(CanvasTerm::class);
    }

    public function unitEnsType(): BelongsTo
    {
        return $this->belongsTo(UnitEnsType::class);
    }

    public function canvasModules(): HasMany
    {
        return $this->hasMany(CanvasModule::class);
    }
}