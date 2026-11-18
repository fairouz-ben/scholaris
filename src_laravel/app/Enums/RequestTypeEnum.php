<?php

namespace App\Enums;

use App\Enums\Concerns\HasEnumHelpers;

enum RequestTypeEnum: string 
{
    use HasEnumHelpers;

    case ABSENCE = 'absence';
    case REPLACEMENT = 'replacement';
    case MAKEUP = 'makeup';
    case SUPPLEMENTARY = 'supplementary';
}
