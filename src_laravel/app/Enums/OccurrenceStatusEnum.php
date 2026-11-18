<?php

namespace App\Enums;

use App\Enums\Concerns\HasEnumHelpers;

enum OccurrenceStatusEnum: string 
{
    use HasEnumHelpers;

    case PLANNED = 'planned';
    case HELD = 'held';
    case CANCELLED = 'cancelled';
}
