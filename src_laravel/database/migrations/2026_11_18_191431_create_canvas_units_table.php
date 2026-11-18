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
        Schema::create('canvas_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('canvas_term_id')->constrained('canvas_terms')->cascadeOnDelete();
            $table->foreignId('unit_ens_type_id')->constrained('unit_ens_types')->restrictOnDelete();
            $table->string('code', 50);          // UEF1.1
            $table->smallInteger('position');
            $table->unsignedTinyInteger('coefficient');
            $table->unsignedTinyInteger('credits'); // sum of its modules (validated in service)
            $table->timestamps();

            $table->unique(['canvas_term_id', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('canvas_units');
    }
};
