<?php

namespace App\Enums;

use App\Enums\Concerns\HasEnumHelpers;

enum EntityStatusEnum: string 
{
    use HasEnumHelpers;

    case ACTIVE = 'active';
    case DISABLED = 'disabled';
    case ARCHIVED = 'archived';
}
