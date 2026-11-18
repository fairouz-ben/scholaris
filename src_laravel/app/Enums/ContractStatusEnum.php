<?php

namespace App\Enums;

use App\Enums\Concerns\HasEnumHelpers;

enum ContractStatusEnum: string 
{
    use HasEnumHelpers;

    case DRAFT = 'draft';
    case SIGNED = 'signed';
    case TERMINATED = 'terminated';
}
