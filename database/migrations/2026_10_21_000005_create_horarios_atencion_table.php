<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('horarios_atencion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medico_id')->constrained('medicos')->cascadeOnDelete();
            $table->unsignedTinyInteger('dia_semana');                // 1 = lunes ... 7 = domingo
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->unsignedSmallInteger('duracion_cita_min')->default(30);
            $table->string('estado', 20)->default('activo');
            $table->timestamps();
            $table->index(['medico_id', 'dia_semana']);
        });

        DB::statement('ALTER TABLE horarios_atencion ADD CONSTRAINT ck_horario_dia CHECK (dia_semana BETWEEN 1 AND 7)');
        DB::statement('ALTER TABLE horarios_atencion ADD CONSTRAINT ck_horario_horas CHECK (hora_fin > hora_inicio)');
    }

    public function down(): void
    {
        Schema::dropIfExists('horarios_atencion');
    }
};
