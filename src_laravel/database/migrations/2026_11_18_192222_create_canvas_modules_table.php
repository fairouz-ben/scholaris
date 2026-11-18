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
        Schema::create('canvas_modules', function (Blueprint $table) {
            $table->id();
      $table->foreignId('canvas_unit_id')->constrained('canvas_units')->cascadeOnDelete();
            $table->string('code', 50);            // UEF1.1.1
            $table->string('name_ar', 255);
            $table->string('name_fr', 255);
            $table->smallInteger('position');
            $table->unsignedTinyInteger('coefficient');
            $table->unsignedTinyInteger('credits');
            $table->unsignedSmallInteger('weekly_cm_minutes')->default(0);
            $table->unsignedSmallInteger('weekly_td_minutes')->default(0);
            $table->unsignedSmallInteger('weekly_tp_minutes')->default(0);
            $table->unsignedSmallInteger('weekly_atelier_minutes')->default(0);
            $table->smallInteger('personal_work_minutes')->nullable();
            $table->unsignedTinyInteger('continuous_percent');
            $table->unsignedTinyInteger('exam_percent');
            $table->timestamps();

            $table->unique(['canvas_unit_id', 'code']);
        });

           // ---- Check: continuous + exam = 100 ----
       /* $driver = DB::connection()->getDriverName();
        if (in_array($driver, ['mysql', 'pgsql', 'sqlsrv'], true)) {
            DB::statement(
                'ALTER TABLE canvas_modules ADD CONSTRAINT chk_eval_percent '
                . 'CHECK (continuous_percent + exam_percent = 100)'
            );
        }*/

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('canvas_modules');
    }
};
