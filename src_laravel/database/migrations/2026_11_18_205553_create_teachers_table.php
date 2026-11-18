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
         Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained('departments')->restrictOnDelete();

            // Identity — Arabic + Latin
            $table->string('last_name_ar', 255);
            $table->string('first_name_ar', 255);
            $table->string('last_name_fr', 255);
            $table->string('first_name_fr', 255);

            // Personal
            $table->date('birth_date');
            $table->string('birth_place', 255)->nullable();
            $table->string('civility', 10)->nullable();          // civility_enum
            $table->string('marital_status', 20)->nullable();    // marital_status_enum
            $table->unsignedTinyInteger('children_count')->nullable();

            // Contact
            $table->string('phone', 20)->nullable();
            $table->string('email', 200)->unique();              // login identifier
            $table->string('password', 255);
            $table->rememberToken();
            $table->string('address', 250)->nullable();
            $table->string('postal_code', 5)->nullable();
            $table->foreignId('wilaya_id')->nullable()->constrained('wilayas')->nullOnDelete();

            // HR
            $table->foreignId('teacher_status_id')->constrained('teacher_status')->restrictOnDelete();
            $table->foreignId('grade_id')->constrained('grades')->restrictOnDelete();
            $table->string('contract_type', 20);                 // contract_type_enum
            $table->string('diploma', 250)->nullable();
            $table->string('diploma_domaine', 200)->nullable();
            $table->string('diploma_filiere', 200)->nullable();
            $table->string('diploma_specialite', 200)->nullable();
            $table->date('hire_date')->nullable();
            $table->string('national_id', 30)->nullable();
            $table->string('ccp_rib', 30)->nullable();
            $table->string('social_security_number', 30)->nullable();
            $table->string('nationality', 100)->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->unique(['last_name_ar', 'first_name_ar', 'birth_date']);
            $table->unique(['last_name_fr', 'first_name_fr', 'birth_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
