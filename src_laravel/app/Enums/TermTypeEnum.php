<?php

namespace App\Enums;

use App\Enums\Concerns\HasEnumHelpers;

enum TermTypeEnum: string 
{
    use HasEnumHelpers;

    case SEMESTER = 'semester';
    case TRIMESTER = 'trimester';
    case QUADRIMESTER = 'quadrimester';
}
