<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Foundation extends Model
{
    protected $fillable = [
        'name_ar',
        'name_fr',
        'address',
        'logo_path',
        'signatory_name',
        'signatory_title_ar',
        'signatory_title_fr',
    ];
}
