<?php

namespace Database\Seeders;

use App\Models\Permiso;
use App\Models\Rol;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermisoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $crud = ['crear', 'mostrar', 'modificar', 'eliminar'];

        $modulos = [
            'usuarios' => $crud,
            'roles' => $crud,
            'pacientes' => $crud,
            'medicos' => $crud,
            'especialidades' => $crud,
            'horarios' => $crud,
            'agenda' => ['ver'],
            'citas' => array_merge($crud, ['reprogramar', 'cancelar', 'confirmar', 'atender']),
            'bitacora' => ['mostrar'],
        ];

        $verbos = [
            'crear' => 'Crear', 'mostrar' => 'Ver', 'modificar' => 'Editar', 'eliminar' => 'Eliminar',
            'reprogramar' => 'Reprogramar', 'cancelar' => 'Cancelar',
            'confirmar' => 'Confirmar', 'atender' => 'Atender',
            'ver' => 'Ver',
        ];

        foreach ($modulos as $modulo => $acciones) {
            foreach ($acciones as $accion) {
                Permiso::firstOrCreate(
                    ['modulo' => $modulo, 'accion' => $accion],
                    ['nombre' => $verbos[$accion] . ' ' . $modulo]
                );
            }
        }

        // Administrador: todo
        $this->asignar('Administrador', Permiso::pluck('id'));

        // Recepcionista: pacientes y citas; en médicos, especialidades y horarios solo consulta
        $this->asignar('Recepcionista', $this->idsPara([
            'pacientes' => ['crear', 'mostrar', 'modificar'],
            'citas' => ['crear', 'mostrar', 'reprogramar', 'cancelar', 'confirmar', 'atender'],
            'medicos' => ['mostrar'],
            'especialidades' => ['mostrar'],
            'horarios' => ['mostrar'],
            'agenda' => ['ver'],
        ]));

        // Médico: solo su agenda (el código limita a sus propias citas)
        $this->asignar('Médico', $this->idsPara([
            'citas' => ['mostrar', 'atender'],
            'pacientes' => ['mostrar'],
            'horarios' => ['mostrar'],
            'agenda' => ['ver'],
        ]));

        // Paciente: solo sus propias citas (el código limita a lo propio)
        $this->asignar('Paciente', $this->idsPara([
            'citas' => ['mostrar'],
        ]));
    }

    private function idsPara(array $modulos)
    {
        return Permiso::where(function ($q) use ($modulos) {
            foreach ($modulos as $modulo => $acciones) {
                $q->orWhere(fn($w) => $w->where('modulo', $modulo)->whereIn('accion', $acciones));
            }
        })->pluck('id');
    }

    private function asignar(string $nombreRol, $ids): void
    {
        $rolId = Rol::where('nombre', $nombreRol)->value('id');

        $filas = collect($ids)->map(fn($id) => [
            'rol_id' => $rolId,
            'permiso_id' => $id,
            'created_at' => now(),
            'updated_at' => now(),
        ])->all();

        DB::table('rol_permisos')->insertOrIgnore($filas);
    }
}
