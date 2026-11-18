<?php

namespace App\Enums;

use App\Enums\Concerns\HasEnumHelpers;

enum MaritalStatusEnum: string 
{
    use HasEnumHelpers;

    case MARRIED = 'married';
    case SINGLE = 'single';
    case WIDOWED = 'widowed';
    case DIVORCED = 'divorced';
}
