<?php

namespace Database\Seeders;

use App\Models\Medico;
use App\Models\Paciente;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $pass = Hash::make(env('SEED_PASSWORD', 'password'));
        $rol = fn ($n) => Rol::where('nombre', $n)->value('id');

        User::firstOrCreate(['email' => 'admin@vidasalud.bo'], ['password' => $pass, 'rol_id' => $rol('Administrador')]);
        User::firstOrCreate(['email' => 'recepcion@vidasalud.bo'], ['password' => $pass, 'rol_id' => $rol('Recepcionista')]);

        // Cuenta de un médico de prueba
        User::firstOrCreate(['email' => 'jperez@vidasalud.bo'], [
            'password' => $pass, 'rol_id' => $rol('Médico'),
            'medico_id' => Medico::where('nro_registro', 'MP-1001')->value('id'),
        ]);

        // Paciente de prueba con su cuenta
        $p = Paciente::firstOrCreate(['ci' => '6543210'], [
            'codigo' => 'PAC-0001', 'nombres' => 'María', 'apellidos' => 'López Rivera',
            'fecha_nacimiento' => '1990-04-12', 'sexo' => 'F', 'telefono' => '70123456',
            'correo' => 'maria@correo.com', 'direccion' => 'Av. Banzer, Santa Cruz',
        ]);
        User::firstOrCreate(['email' => 'maria@correo.com'], ['password' => $pass, 'rol_id' => $rol('Paciente'), 'paciente_id' => $p->id]);
    }
}
