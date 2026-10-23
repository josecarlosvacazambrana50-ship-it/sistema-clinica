<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('citas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 12)->nullable()->unique();       // CM-0001
            $table->foreignId('paciente_id')->constrained('pacientes')->restrictOnDelete();
            $table->foreignId('medico_id')->constrained('medicos')->restrictOnDelete();
            $table->foreignId('especialidad_id')->constrained('especialidades')->restrictOnDelete();
            $table->foreignId('registrada_por')->constrained('users')->restrictOnDelete();
            $table->date('fecha');
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->string('motivo', 200)->nullable();
            // programada, confirmada, atendida, cancelada, inasistencia
            $table->string('estado', 20)->default('programada');
            $table->string('motivo_cancelacion', 200)->nullable();
            $table->timestamps();
            $table->index(['fecha', 'medico_id']);
            $table->index(['paciente_id', 'fecha']);
            $table->index('estado');
        });

        DB::statement('ALTER TABLE citas ADD CONSTRAINT ck_cita_horas CHECK (hora_fin > hora_inicio)');
        DB::statement("ALTER TABLE citas ADD CONSTRAINT ck_cita_estado CHECK (estado IN ('programada','confirmada','atendida','cancelada','inasistencia'))");

        // RF07 / RN07: un médico no puede tener dos citas superpuestas (las canceladas no cuentan).
        // Esta restricción exclusiva solo existe en PostgreSQL.
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('CREATE EXTENSION IF NOT EXISTS btree_gist');
            DB::statement("ALTER TABLE citas ADD CONSTRAINT ex_cita_sin_solape EXCLUDE USING gist (
                medico_id WITH =,
                tsrange(fecha + hora_inicio, fecha + hora_fin) WITH &&
            ) WHERE (estado <> 'cancelada')");
        }

        // Trazabilidad de los cambios de estado de cada cita
        Schema::create('historial_estado_citas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cita_id')->constrained('citas')->restrictOnDelete();
            $table->string('estado_anterior', 20)->nullable();
            $table->string('estado_nuevo', 20);
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->string('observacion', 200)->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historial_estado_citas');
        Schema::dropIfExists('citas');
    }
};
