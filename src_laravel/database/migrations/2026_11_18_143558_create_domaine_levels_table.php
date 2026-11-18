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
        Schema::create('domaine_levels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('domaine_id')->constrained('domaines')->cascadeOnDelete();
            $table->foreignId('cycle_level_id')->constrained('cycle_levels')->cascadeOnDelete();
            $table->boolean('tronc_commun')->default(false);
            $table->timestamps();

            $table->unique(['domaine_id', 'cycle_level_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('domaine_levels');
    }
};
