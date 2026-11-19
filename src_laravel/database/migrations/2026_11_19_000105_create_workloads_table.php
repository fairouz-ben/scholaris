<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workloads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teachers')->restrictOnDelete();
            $table->foreignId('academic_year_term_id')->constrained('academic_year_terms')->restrictOnDelete();
            $table->string('period_type', 10);                      // workload_period_enum
            $table->date('period_start');
            $table->date('period_end');
            $table->unsignedTinyInteger('weeks');

            // Snapshot computed by WorkloadCalculator from session_occurrences — never edited by hand.
            $table->unsignedInteger('raw_minutes');                 // real duration of counted sessions
            $table->decimal('equivalent_minutes', 9, 2);            // gross, in "équivalent cours"
            $table->decimal('absence_deduction_minutes', 9, 2);
            $table->decimal('net_minutes', 9, 2);                   // equivalent - deduction
            $table->unsignedInteger('obligation_minutes');          // 0 for vacataires
            $table->unsignedInteger('cap_minutes');
            $table->decimal('paid_minutes', 9, 2);                  // min(net, cap)
            $table->decimal('overtime_minutes', 9, 2);              // paid above obligation (permanent only)
            $table->decimal('excess_minutes', 9, 2);                // net above cap, not paid
            $table->string('status', 20)->default('normal');        // workload_status_enum
            $table->timestamp('calculated_at');
            $table->timestamps();

            $table->unique(
                ['teacher_id', 'academic_year_term_id', 'period_type', 'period_start'],
                'workloads_period_unique'
            );
            $table->index(['academic_year_term_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workloads');
    }
};
