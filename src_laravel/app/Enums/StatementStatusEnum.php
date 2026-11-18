<?php

namespace App\Enums;

use App\Enums\Concerns\HasEnumHelpers;

enum StatementStatusEnum: string 
{
    use HasEnumHelpers;

    case DRAFT = 'draft';
    case FINAL = 'final';
}
