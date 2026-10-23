<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pacientes', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 12)->nullable()->unique();      // PAC-0001
            $table->string('ci', 20)->unique();                      // documento de identidad único
            $table->string('nombres', 80);
            $table->string('apellidos', 80);
            $table->date('fecha_nacimiento');
            $table->char('sexo', 1)->nullable();                     // M, F u O
            $table->string('telefono', 20);
            $table->string('correo', 120)->nullable();
            $table->string('direccion', 200)->nullable();
            // Información médica básica (perfil del paciente)
            $table->string('tipo_sangre', 3)->nullable();
            $table->text('alergias')->nullable();
            $table->text('enfermedades_cronicas')->nullable();
            $table->text('medicamentos')->nullable();
            $table->text('antecedentes')->nullable();
            // Contacto de emergencia
            $table->string('emergencia_nombre', 100)->nullable();
            $table->string('emergencia_parentesco', 40)->nullable();
            $table->string('emergencia_telefono', 20)->nullable();
            $table->string('estado', 20)->default('activo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pacientes');
    }
};
