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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('matricule', 30)->unique();          // login identifier
            $table->string('email', 200)->nullable()->unique();
            $table->string('password', 255);
            $table->rememberToken();

            $table->string('last_name_ar', 255);
            $table->string('first_name_ar', 255);
            $table->string('last_name_fr', 255);
            $table->string('first_name_fr', 255);

            $table->date('birth_date');
            $table->string('birth_place', 255)->nullable();

            $table->foreignId('speciality_id')->nullable()->constrained('specialities')->nullOnDelete();
            $table->string('status', 20)->default('active');    // student_status_enum

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
