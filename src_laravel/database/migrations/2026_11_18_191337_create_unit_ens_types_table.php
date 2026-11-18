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
        Schema::create('unit_ens_types', function (Blueprint $table) {
            $table->id();
             $table->string('name_ar', 255);
            $table->string('name_fr', 255);  // UE fondamentales, methodologie, transversale
            $table->string('code_ar', 20);
            $table->string('code_fr', 20);   // UEF, UEM, UET
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_ens_types');
    }
};
