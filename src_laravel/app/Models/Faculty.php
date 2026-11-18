<?php

namespace App\Models;

use App\Enums\LanguageEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Faculty extends Model
{
    protected $fillable = [
        'foundation_id',
        'name_ar',
        'name_fr',
        'default_language',
        'address',
        'logo_path',
        'signatory_name',
        'signatory_title_ar',
        'signatory_title_fr',
    ];

    protected $casts = [
        'default_language' => LanguageEnum::class,
    ];

    public function foundation(): BelongsTo
    {
        return $this->belongsTo(Foundation::class);
    }
}