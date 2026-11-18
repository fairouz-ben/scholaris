<?php

namespace App\Enums;

use App\Enums\Concerns\HasEnumHelpers;

enum DocumentTypeEnum: string 
{
    use HasEnumHelpers;

    case CANVAS = 'canvas';
    case ADDITIONAL_LESSONS_LICENSE = 'additional_lessons_license';
    case VACATION_LIST = 'vacation_list';
    case SUPPLEMENTARY_LESSONS_FORM = 'supplementary_lessons_form';
    case PEDAGOGICAL_RESOURCE_SHEET = 'pedagogical_resource_sheet';
    case EMPLOYMENT_CONTRACT = 'employment_contract';
}
