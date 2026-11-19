<?php

namespace App\Models;

use App\Enums\WorkloadPeriodEnum;
use App\Enums\WorkloadStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Workload extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'academic_year_term_id',
        'period_type',
        'period_start',
        'period_end',
        'weeks',
        'raw_minutes',
        'equivalent_minutes',
        'absence_deduction_minutes',
        'net_minutes',
        'obligation_minutes',
        'cap_minutes',
        'paid_minutes',
        'overtime_minutes',
        'excess_minutes',
        'status',
        'calculated_at',
    ];

    protected $casts = [
        'period_type'               => WorkloadPeriodEnum::class,
        'status'                    => WorkloadStatusEnum::class,
        'period_start'              => 'date',
        'period_end'                => 'date',
        'weeks'                     => 'integer',
        'raw_minutes'               => 'integer',
        'equivalent_minutes'        => 'decimal:2',
        'absence_deduction_minutes' => 'decimal:2',
        'net_minutes'               => 'decimal:2',
        'obligation_minutes'        => 'integer',
        'cap_minutes'               => 'integer',
        'paid_minutes'              => 'decimal:2',
        'overtime_minutes'          => 'decimal:2',
        'excess_minutes'            => 'decimal:2',
        'calculated_at'             => 'datetime',
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function academicYearTerm(): BelongsTo
    {
        return $this->belongsTo(AcademicYearTerm::class);
    }
}
