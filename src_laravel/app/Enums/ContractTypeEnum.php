<?php

namespace App\Enums;
use App\Enums\Concerns\HasEnumHelpers;
enum ContractTypeEnum: string
{
    use HasEnumHelpers;
    case PERMANENT = 'permanent';
    case VACATAIRE = 'vacataire';

    
}