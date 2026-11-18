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
        Schema::create('speciality_levels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('speciality_id')->constrained('specialities')->cascadeOnDelete();
            $table->foreignId('cycle_level_id')->constrained('cycle_levels')->cascadeOnDelete();
            $table->string('status', 20)->default('active');
            $table->timestamps();

            $table->unique(['speciality_id', 'cycle_level_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('speciality_levels');
    }
};
