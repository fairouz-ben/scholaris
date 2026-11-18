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
        Schema::create('terms', function (Blueprint $table) {
            $table->id();
                        $table->string('term_type', 20)->default('semester');
            $table->string('name_ar', 255);
            $table->string('name_fr', 255);
            $table->unsignedTinyInteger('term_number')->default(1);
            $table->string('code', 20)->unique();     // S1, S2, ...
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('terms');
    }
};
