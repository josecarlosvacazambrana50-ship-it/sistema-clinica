<?php

namespace App\Models;

use App\Observers\BitacoraObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy(BitacoraObserver::class)]
class Cita extends Model
{
    protected $fillable = [
        'codigo', 'paciente_id', 'medico_id', 'especialidad_id', 'registrada_por',
        'fecha', 'hora_inicio', 'hora_fin', 'motivo', 'estado', 'motivo_cancelacion',
    ];

    public const ESTADOS = [
        'programada'   => 'Programada',
        'confirmada'   => 'Confirmada',
        'atendida'     => 'Atendida',
        'cancelada'    => 'Cancelada',
        'inasistencia' => 'Inasistencia',
    ];

    protected function casts(): array
    {
        return ['fecha' => 'date'];
    }

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function medico()
    {
        return $this->belongsTo(Medico::class);
    }

    public function especialidad()
    {
        return $this->belongsTo(Especialidad::class);
    }

    public function registradaPor()
    {
        return $this->belongsTo(User::class, 'registrada_por');
    }

    public function historial()
    {
        return $this->hasMany(HistorialEstadoCita::class)->orderBy('id');
    }

    public function getHoraInicioCortaAttribute(): string
    {
        return substr($this->hora_inicio, 0, 5);
    }

    public function getHoraFinCortaAttribute(): string
    {
        return substr($this->hora_fin, 0, 5);
    }

    /** Las citas canceladas no ocupan horario (RN09). */
    public function scopeOcupan($q)
    {
        return $q->where('estado', '!=', 'cancelada');
    }
}
