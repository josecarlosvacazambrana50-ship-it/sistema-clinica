<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email', 100)->unique();
            $table->string('password');                               // hash (bcrypt)
            $table->string('estado', 20)->default('activo');
            $table->timestamp('ultimo_acceso')->nullable();
            $table->foreignId('rol_id')->constrained('rols')->restrictOnDelete();
            // Cuenta vinculada a un paciente o a un médico (el personal administrativo no tiene ninguno)
            $table->foreignId('paciente_id')->nullable()->unique()->constrained('pacientes')->restrictOnDelete();
            $table->foreignId('medico_id')->nullable()->unique()->constrained('medicos')->restrictOnDelete();
            $table->rememberToken();
            $table->timestamps();
        });

        // Regla: una cuenta no puede ser de paciente y de médico a la vez
        DB::statement('ALTER TABLE users ADD CONSTRAINT users_paciente_o_medico CHECK (
            paciente_id IS NULL OR medico_id IS NULL
        )');

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
