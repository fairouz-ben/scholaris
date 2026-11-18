<?php

namespace App\Enums;

use App\Enums\Concerns\HasEnumHelpers;

enum WorkloadStatusEnum: string 
{
    use HasEnumHelpers;

    case NORMAL = 'normal';
    case OVERLOAD = 'overload';
    case CRITICAL = 'critical';
}
