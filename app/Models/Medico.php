<?php

namespace App\Models;

use App\Observers\BitacoraObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy(BitacoraObserver::class)]
class Medico extends Model
{
    protected $fillable = ['codigo', 'nro_registro', 'nombres', 'apellidos', 'telefono', 'correo', 'estado'];

    public function getNombreCompletoAttribute(): string
    {
        return trim($this->nombres . ' ' . $this->apellidos);
    }

    public function especialidades()
    {
        return $this->belongsToMany(Especialidad::class, 'medico_especialidad');
    }

    public function horarios()
    {
        return $this->hasMany(HorarioAtencion::class)->orderBy('dia_semana')->orderBy('hora_inicio');
    }

    public function citas()
    {
        return $this->hasMany(Cita::class);
    }

    public function usuario()
    {
        return $this->hasOne(User::class);
    }

    public function scopeActivos($q)
    {
        return $q->where('estado', 'activo');
    }
}
