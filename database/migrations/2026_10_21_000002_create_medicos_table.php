<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medicos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 12)->nullable()->unique();      // MED-0001
            $table->string('nro_registro', 30)->unique();            // registro profesional
            $table->string('nombres', 80);
            $table->string('apellidos', 80);
            $table->string('telefono', 20)->nullable();
            $table->string('correo', 120)->nullable();
            $table->string('estado', 20)->default('activo');
            $table->timestamps();
        });

        // Un médico puede tener varias especialidades y viceversa
        Schema::create('medico_especialidad', function (Blueprint $table) {
            $table->foreignId('medico_id')->constrained('medicos')->cascadeOnDelete();
            $table->foreignId('especialidad_id')->constrained('especialidades')->restrictOnDelete();
            $table->primary(['medico_id', 'especialidad_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medico_especialidad');
        Schema::dropIfExists('medicos');
    }
};
