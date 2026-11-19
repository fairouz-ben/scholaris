<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_term_id')->constrained('academic_year_terms')->restrictOnDelete();
            $table->foreignId('canvas_module_id')->constrained('canvas_modules')->restrictOnDelete();
            $table->foreignId('section_id')->constrained('sections')->cascadeOnDelete();
            $table->foreignId('section_group_id')->nullable()->constrained('section_groups')->nullOnDelete();
            $table->foreignId('teacher_id')->constrained('teachers')->restrictOnDelete();
            $table->foreignId('room_id')->constrained('rooms')->restrictOnDelete();
            $table->foreignId('session_type_id')->constrained('session_types')->restrictOnDelete();
            $table->foreignId('session_category_id')->constrained('session_categories')->restrictOnDelete();
            $table->unsignedTinyInteger('day_of_week');            // 1-7 ISO
            $table->foreignId('time_slot_id')->constrained('time_slots')->restrictOnDelete();
            $table->string('week_pattern', 10)->default('every');  // week_pattern_enum
            $table->date('start_date');
            $table->date('end_date')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->index(['room_id', 'day_of_week', 'time_slot_id'], 'schedules_room_slot_index');
            $table->index(['teacher_id', 'day_of_week', 'time_slot_id'], 'schedules_teacher_slot_index');
            $table->index(['section_id', 'day_of_week', 'time_slot_id'], 'schedules_section_slot_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
