<?php

namespace App\Enums;

use App\Enums\Concerns\HasEnumHelpers;

enum CivilityEnum: string
{
    use HasEnumHelpers;

    case M = 'M';
    case MME = 'MME';
}
