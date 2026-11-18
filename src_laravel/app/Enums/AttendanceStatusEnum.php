<?php

namespace App\Enums;

use App\Enums\Concerns\HasEnumHelpers;

enum AttendanceStatusEnum: string
{
    use HasEnumHelpers;

    case PRESENT = 'present';
    case ABSENT = 'absent';
    case JUSTIFIED = 'justified';
}
