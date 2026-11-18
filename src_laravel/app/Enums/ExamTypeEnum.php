<?php

namespace App\Enums;

use App\Enums\Concerns\HasEnumHelpers;

enum ExamTypeEnum: string 
{
    use HasEnumHelpers;

    case NORMAL = 'normal';
    case RESIT = 'resit';
}
