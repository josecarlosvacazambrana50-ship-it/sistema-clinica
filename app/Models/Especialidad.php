<?php

namespace App\Models;

use App\Observers\BitacoraObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

#[ObservedBy(BitacoraObserver::class)]
class Especialidad extends Model
{
    protected $table = 'especialidades';

    protected $fillable = ['nombre', 'nombre_normalizado', 'descripcion', 'estado'];

    /** Normaliza un nombre: minúsculas, sin tildes y sin espacios repetidos (RF04). */
    public static function normalizar(string $nombre): string
    {
        return Str::of(Str::ascii($nombre))->lower()->squish()->toString();
    }

    public function medicos()
    {
        return $this->belongsToMany(Medico::class, 'medico_especialidad');
    }

    public function citas()
    {
        return $this->hasMany(Cita::class);
    }

    public function scopeActivas($q)
    {
        return $q->where('estado', 'activo');
    }
}
