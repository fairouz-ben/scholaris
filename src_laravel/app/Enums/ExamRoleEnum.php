<?php

namespace App\Enums;

use App\Enums\Concerns\HasEnumHelpers;

enum ExamRoleEnum: string 
{
    use HasEnumHelpers;

    case HEAD = 'head';
    case SUPERVISOR = 'supervisor';
}
