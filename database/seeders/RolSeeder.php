<?php

namespace Database\Seeders;

use App\Models\Rol;
use Illuminate\Database\Seeder;

class RolSeeder extends Seeder
{
    public function run(): void
    {
        Rol::firstOrCreate(['nombre' => 'Administrador'], ['descripcion' => 'Configura y supervisa el sistema.']);
        Rol::firstOrCreate(['nombre' => 'Recepcionista'], ['descripcion' => 'Gestiona pacientes y citas médicas.']);
        Rol::firstOrCreate(['nombre' => 'Médico'], ['descripcion' => 'Consulta su agenda de atención.']);
        Rol::firstOrCreate(['nombre' => 'Paciente'], ['descripcion' => 'Consulta su perfil y sus citas.']);
    }
}
