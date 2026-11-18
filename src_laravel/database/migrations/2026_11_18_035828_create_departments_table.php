<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('faculty_id')
                  ->constrained()
                  ->restrictOnDelete();
            $table->string('name_ar');
            $table->string('name_fr');

            // FK to teachers is added in a later migration (circular dependency).
            $table->unsignedBigInteger('head_teacher_id')->nullable();
            $table->index('head_teacher_id');

            $table->string('signatory_title_ar')->nullable();
            $table->string('signatory_title_fr')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
