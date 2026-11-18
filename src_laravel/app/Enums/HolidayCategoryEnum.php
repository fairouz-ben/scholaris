<?php

namespace App\Enums;

use App\Enums\Concerns\HasEnumHelpers;

enum HolidayCategoryEnum: string
{
    use HasEnumHelpers;

    case ACADEMIC = 'academic';
    case NATIONAL = 'national';
    case RELIGIOUS = 'religious';
    case EXCEPTION = 'exception';
}
