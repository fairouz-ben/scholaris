<?php

namespace App\Enums;

use App\Enums\Concerns\HasEnumHelpers;

enum RequestStatusEnum: string 
{
    use HasEnumHelpers;

    case PENDING = 'pending';
    case VALIDATED = 'validated';
    case REJECTED = 'rejected';
    case CANCELLED = 'cancelled';
}
