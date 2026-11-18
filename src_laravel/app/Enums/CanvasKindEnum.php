<?php

namespace App\Enums;

use App\Enums\Concerns\HasEnumHelpers;

enum CanvasKindEnum: string 
{
    use HasEnumHelpers;

    case BASE = 'base';
    case AMENDMENT = 'amendment';
}
