<?php

namespace App\Enums;

use App\Enums\Concerns\HasEnumHelpers;

enum StudentStatusEnum: string
{
    use HasEnumHelpers;

    case ACTIVE = 'active';
    case SUSPENDED = 'suspended';
    case GRADUATED = 'graduated';
    case DROPPED = 'dropped';
    
    //case DroppedOut = 'dropped_out';
    //case Withdrawn  = 'withdrawn';
}
