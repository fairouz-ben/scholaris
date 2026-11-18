<?php

namespace App\Enums;

use App\Enums\Concerns\HasEnumHelpers;

enum JuryRoleEnum: string 
{
    use HasEnumHelpers;

    case PRESIDENT = 'president';
    case EXAMINER = 'examiner';
    case SUPERVISOR = 'supervisor';
    case CO_SUPERVISOR = 'co_supervisor';
}
