<?php

namespace App\Models;

use App\Enums\HolidayCategoryEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Holiday extends Model
{
    protected $fillable = [
        'name_ar',
        'name_fr',
        'is_fixed',
        'category',
    ];

    protected $casts = [
        'is_fixed' => 'boolean',
        'category' => HolidayCategoryEnum::class,
    ];

    public function yearlyHolidays(): HasMany
    {
        return $this->hasMany(YearlyHoliday::class);
    }
}
