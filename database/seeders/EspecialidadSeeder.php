<?php

namespace Database\Seeders;

use App\Models\Especialidad;
use Illuminate\Database\Seeder;

class EspecialidadSeeder extends Seeder
{
    public function run(): void
    {
        $lista = [
            ['Medicina general', 'Atención médica integral'],
            ['Pediatría', 'Atención de niños y adolescentes'],
            ['Ginecología', 'Salud de la mujer'],
            ['Odontología', 'Salud bucal'],
            ['Cardiología', 'Enfermedades del corazón'],
        ];
        foreach ($lista as [$nombre, $desc]) {
            Especialidad::firstOrCreate(
                ['nombre_normalizado' => Especialidad::normalizar($nombre)],
                ['nombre' => $nombre, 'descripcion' => $desc]
            );
        }
    }
}
