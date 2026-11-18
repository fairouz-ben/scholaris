<?php

namespace App\Enums;

use App\Enums\Concerns\HasEnumHelpers;

enum RoomTypeEnum: string 
{
    use HasEnumHelpers;

    case CM = 'cm';
    case TD = 'td';
    case TP = 'tp';
    case AMPHI = 'amphi';
    case LABO = 'labo';
    case SALLE = 'salle';
}
