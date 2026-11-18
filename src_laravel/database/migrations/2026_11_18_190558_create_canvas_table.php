<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('canvas', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 100)->unique();
            $table->string('kind', 20)->default('base'); // canvas_kind_enum
            $table->foreignId('parent_canvas_id')
                  ->nullable()
                  ->constrained('canvas')
                  ->nullOnDelete();                        // amendment → nulls if base hard-deleted
            $table->string('name_ar', 255);
            $table->string('name_fr', 255);
            $table->string('canvas_year', 10);             // '2020-2021'
            $table->foreignId('cycle_level_id')->constrained('cycle_levels')->cascadeOnDelete();
            $table->foreignId('speciality_id')->nullable()->constrained('specialities')->nullOnDelete();
            $table->foreignId('domaine_id')->nullable()->constrained('domaines')->nullOnDelete();
            $table->unsignedTinyInteger('total_weeks')->default(14);
            $table->string('status', 20)->default('active'); // entity_status_enum
            $table->softDeletes();
            $table->timestamps();

            $table->index(['speciality_id', 'cycle_level_id', 'canvas_year']);
        });

        // ---- Check constraint: needs specialite OR domaine ----
        // MySQL 8.0.16+ / PostgreSQL / SQL Server only.
        // SQLite: not supported post-creation — enforce in FormRequest / observer.
       /* $driver = DB::connection()->getDriverName();
        if (in_array($driver, ['mysql', 'pgsql', 'sqlsrv'], true)) {
            DB::statement(
                'ALTER TABLE canvas ADD CONSTRAINT chk_canvas_scope '
                . 'CHECK (speciality_id IS NOT NULL OR domaine_id IS NOT NULL)'
            );
        }*/
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('canvas');
    }
};
