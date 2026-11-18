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
        Schema::create('cycle_levels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cycle_id')->constrained('cycles')->cascadeOnDelete();
            $table->string('name_ar', 255);
            $table->string('name_fr', 255);
            $table->string('code_ar', 10);
            $table->string('code_fr', 10);
            $table->string('status', 20)->default('active');
            $table->timestamps();

            $table->unique(['cycle_id', 'name_ar']);
            $table->unique(['cycle_id', 'name_fr']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cycle_levels');
    }
};
