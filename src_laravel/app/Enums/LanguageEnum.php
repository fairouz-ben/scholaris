<?php

namespace App\Enums;

use App\Enums\Concerns\HasEnumHelpers;

enum LanguageEnum: string 
{
    use HasEnumHelpers;

    case AR = 'ar';
    case FR = 'fr';
}
