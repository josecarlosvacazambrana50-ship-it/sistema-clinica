<?php

namespace App\Models;

use App\Observers\BitacoraObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy(BitacoraObserver::class)]
class Paciente extends Model
{
    protected $fillable = [
        'codigo', 'ci', 'nombres', 'apellidos', 'fecha_nacimiento', 'sexo', 'telefono', 'correo', 'direccion',
        'tipo_sangre', 'alergias', 'enfermedades_cronicas', 'medicamentos', 'antecedentes',
        'emergencia_nombre', 'emergencia_parentesco', 'emergencia_telefono', 'estado',
    ];

    protected function casts(): array
    {
        return ['fecha_nacimiento' => 'date'];
    }

    /**
     * Evento ejecutado al inicializar el modelo para la creación automática de códigos.
     */
    protected static function booted()
    {
        static::creating(function ($paciente) {
            if (empty($paciente->codigo)) {
                $ultimoPaciente = self::latest('id')->first();
                
                if ($ultimoPaciente && $ultimoPaciente->codigo) {
                    $numero = intval(str_replace('PAC-', '', $ultimoPaciente->codigo)) + 1;
                } else {
                    $numero = 1;
                }

                $paciente->codigo = 'PAC-' . str_pad($numero, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function getNombreCompletoAttribute(): string
    {
        return trim($this->apellidos . ', ' . $this->nombres);
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

    /** Búsqueda por CI, nombre o apellido (RF02). */
    public function scopeBuscar($q, ?string $texto)
    {
        if (! $texto) {
            return $q;
        }
        $like = '%' . mb_strtolower($texto) . '%';

        return $q->where(fn ($w) => $w->where('ci', 'like', "%{$texto}%")
            ->orWhereRaw('lower(nombres) like ?', [$like])
            ->orWhereRaw('lower(apellidos) like ?', [$like]));
    }
}
