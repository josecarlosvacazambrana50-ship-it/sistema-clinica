<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permiso extends Model
{
    protected $fillable = [
        'nombre',
        'modulo',
        'accion'
    ];

    // Nombres legibles de cada módulo y acción para la pantalla
public const MODULOS = [
    'usuarios'       => 'Usuarios',
    'roles'          => 'Roles y permisos',
    'pacientes'      => 'Pacientes',
    'medicos'        => 'Médicos',
    'especialidades' => 'Especialidades',
    'horarios'       => 'Horarios de atención',
    'citas'          => 'Citas médicas',
    'bitacora'       => 'Bitácora',
];

public const ACCIONES = [
    'crear'     => 'Crear',
    'mostrar'   => 'Ver',
    'modificar' => 'Editar',
    'eliminar'  => 'Eliminar',
];

    public function roles(){
        return $this->belongsToMany(Rol::class, 'rol_permisos')->withTimestamps();
    }
}
