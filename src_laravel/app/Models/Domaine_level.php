<?php

namespace App\Models;

use App\Models\Domaine;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Domaine_level extends Model
{
     protected $fillable = [
        'domaine_id',
        'cycle_level_id',
        'tronc_commun',
    ];

    protected $casts = [
        'tronc_commun' => 'boolean',
    ];

    public function domaine(): BelongsTo
    {
        return $this->belongsTo(Domaine::class);
    }

    public function cycleLevel(): BelongsTo
    {
        return $this->belongsTo(Cycle_level::class);
    }
}
