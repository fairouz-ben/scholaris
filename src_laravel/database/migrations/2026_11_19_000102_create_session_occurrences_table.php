<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('session_occurrences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained('schedules')->cascadeOnDelete();
            $table->date('date');
            $table->string('status', 20)->default('planned');      // occurrence_status_enum
            $table->foreignId('teacher_id')->constrained('teachers')->restrictOnDelete();
            $table->foreignId('room_id')->nullable()->constrained('rooms')->nullOnDelete();

            // FK to teacher_requests is added in the teacher_requests migration (circular dependency).
            $table->unsignedBigInteger('teacher_request_id')->nullable();
            $table->index('teacher_request_id');

            $table->timestamps();

            $table->unique(['schedule_id', 'date']);
            $table->index(['teacher_id', 'date']);
            $table->index(['date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('session_occurrences');
    }
};
