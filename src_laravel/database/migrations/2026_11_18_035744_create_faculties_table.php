<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\LanguageEnum;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('faculties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('foundation_id')
                  ->constrained()
                  ->restrictOnDelete();
            $table->string('name_ar');
            $table->string('name_fr');
            $table->enum('default_language', array_column(LanguageEnum::cases(), 'value'));
            $table->string('address')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('signatory_name')->nullable();
            $table->string('signatory_title_ar')->nullable();
            $table->string('signatory_title_fr')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faculties');
    }
};
