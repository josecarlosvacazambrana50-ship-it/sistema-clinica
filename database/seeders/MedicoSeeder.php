<?php

namespace Database\Seeders;

use App\Models\Especialidad;
use App\Models\HorarioAtencion;
use App\Models\Medico;
use Illuminate\Database\Seeder;

class MedicoSeeder extends Seeder
{
    public function run(): void
    {
        $datos = [
            ['MP-1001', 'Juan', 'Pérez', 'Medicina general', [[1, '08:00', '12:00'], [3, '14:00', '18:00']]],
            ['MP-1002', 'Ana', 'Soto', 'Pediatría', [[2, '09:00', '13:00'], [4, '09:00', '13:00']]],
            ['MP-1003', 'Luis', 'Mamani', 'Cardiología', [[5, '08:00', '12:00']]],
        ];
        foreach ($datos as $i => [$reg, $nom, $ape, $esp, $horarios]) {
            $m = Medico::firstOrCreate(['nro_registro' => $reg], [
                'codigo' => 'MED-' . str_pad((string) ($i + 1), 4, '0', STR_PAD_LEFT),
                'nombres' => $nom, 'apellidos' => $ape,
                'telefono' => '7000000' . ($i + 1), 'correo' => strtolower($nom) . '@vidasalud.bo',
            ]);
            $m->especialidades()->syncWithoutDetaching([Especialidad::where('nombre', $esp)->value('id')]);
            if ($m->horarios()->count() === 0) {
                foreach ($horarios as [$dia, $ini, $fin]) {
                    HorarioAtencion::create(['medico_id' => $m->id, 'dia_semana' => $dia, 'hora_inicio' => $ini, 'hora_fin' => $fin, 'duracion_cita_min' => 30]);
                }
            }
        }
    }
}
