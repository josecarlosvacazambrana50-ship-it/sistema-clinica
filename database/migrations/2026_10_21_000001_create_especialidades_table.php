<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('especialidades', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 60);
            // Nombre en minúsculas y sin tildes: evita especialidades repetidas (RF04)
            $table->string('nombre_normalizado', 60)->unique();
            $table->string('descripcion', 200)->nullable();
            $table->string('estado', 20)->default('activo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('especialidades');
    }
};
