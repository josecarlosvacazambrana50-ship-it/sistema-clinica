<?php

namespace App\Models;

use App\Observers\BitacoraObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy(BitacoraObserver::class)]
class HorarioAtencion extends Model
{
    protected $table = 'horarios_atencion';

    protected $fillable = ['medico_id', 'dia_semana', 'hora_inicio', 'hora_fin', 'duracion_cita_min', 'estado'];

    public const DIAS = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'];

    public const DURACIONES = [15, 20, 30, 45, 60];

    public function medico()
    {
        return $this->belongsTo(Medico::class);
    }

    public function getDiaNombreAttribute(): string
    {
        return self::DIAS[$this->dia_semana] ?? '';
    }

    public function scopeActivos($q)
    {
        return $q->where('estado', 'activo');
    }
}
