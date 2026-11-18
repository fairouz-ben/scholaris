<?php

namespace App\Enums;

use App\Enums\Concerns\HasEnumHelpers;

enum WorkloadPeriodEnum: string 
{
    use HasEnumHelpers;

    case WEEK = 'week';
    case MONTH = 'month';
    case TERM = 'term';
    case YEAR = 'year';
}
