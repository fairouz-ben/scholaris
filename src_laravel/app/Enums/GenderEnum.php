<?php

namespace App\Enums;

use App\Enums\Concerns\HasEnumHelpers;

enum GenderEnum: string 
{
    use HasEnumHelpers;

    case M = 'M';
    case F = 'F';
}
