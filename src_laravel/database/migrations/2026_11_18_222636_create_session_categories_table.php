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
        Schema::create('session_categories', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique();       // ord, rep, ratt, supp
            $table->string('name_ar', 200)->unique();
            $table->string('name_fr', 200)->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('session_categories');
    }
};
