<?php

namespace App\Models;

use App\Enums\RoomTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'department_id',
        'type',
        'name',
        'capacity',
        'location',
        'equipment',
        'is_active',
        'is_shared',
    ];

    protected $casts = [
        'type'      => RoomTypeEnum::class,
        'capacity'  => 'integer',
        'location'  => 'array',
        'equipment' => 'array',
        'is_active' => 'boolean',
        'is_shared' => 'boolean',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
}