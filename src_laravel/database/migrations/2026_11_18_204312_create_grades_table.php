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
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
              $table->string('name_ar', 255)->unique();
            $table->string('name_fr', 255)->unique();
            $table->string('code_ar', 50)->unique();
            $table->string('code_fr', 50)->unique();
            $table->unsignedTinyInteger('priority')->default(0);
            $table->boolean('can_teach_cm')->default(false);
            $table->boolean('priority_for_uef')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
