<?php

namespace App\Enums;

use App\Enums\Concerns\HasEnumHelpers;

enum WeekPatternEnum: string 
{
    use HasEnumHelpers;

    case EVERY = 'every';
    case ODD = 'odd';
    case EVEN = 'even';
}
