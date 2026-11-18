<?php

namespace App\Enums;

use App\Enums\Concerns\HasEnumHelpers;

enum EnrollmentStatusEnum: string 
{
    use HasEnumHelpers;

    case ENROLLED = 'enrolled';
    case WITHDRAWN = 'withdrawn';
    case COMPLETED = 'completed';
}
