<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years')->restrictOnDelete();
            $table->foreignId('canvas_module_id')->constrained('canvas_modules')->restrictOnDelete();
            $table->foreignId('section_id')->constrained('sections')->cascadeOnDelete();
            $table->foreignId('session_type_id')->constrained('session_types')->restrictOnDelete();
            $table->boolean('is_temporary')->default(false);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();

            $table->unique(
                ['teacher_id', 'academic_year_id', 'canvas_module_id', 'section_id', 'session_type_id'],
                'teacher_assignments_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_assignments');
    }
};
