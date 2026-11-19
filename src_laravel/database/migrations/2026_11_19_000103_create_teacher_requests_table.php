<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teachers')->restrictOnDelete();
            $table->string('type', 20);                            // request_type_enum
            $table->string('status', 20)->default('pending');      // request_status_enum
            $table->foreignId('absence_type_id')->nullable()->constrained('absence_types')->nullOnDelete();
            $table->foreignId('session_occurrence_id')->nullable()->constrained('session_occurrences')->nullOnDelete();
            $table->foreignId('replacement_teacher_id')->nullable()->constrained('teachers')->nullOnDelete();
            $table->text('reason')->nullable();
            $table->foreignId('validated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('validated_at')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->index(['status', 'type']);
            $table->index(['teacher_id', 'status']);
        });

        Schema::table('session_occurrences', function (Blueprint $table) {
            $table->foreign('teacher_request_id')
                  ->references('id')->on('teacher_requests')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('session_occurrences', function (Blueprint $table) {
            $table->dropForeign(['teacher_request_id']);
        });

        Schema::dropIfExists('teacher_requests');
    }
};
