<?php

namespace App\Enums;

use App\Enums\Concerns\HasEnumHelpers;

enum CalendarEventTypeEnum: string 
{
    use HasEnumHelpers;

    case TEACHING = 'teaching';
    case EXAM = 'exam';
    case RESIT = 'resit';
    case DEFENSE = 'defense';
}
