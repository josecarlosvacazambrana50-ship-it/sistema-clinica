<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistorialEstadoCita extends Model
{
    const UPDATED_AT = null;

    protected $table = 'historial_estado_citas';

    protected $fillable = ['cita_id', 'estado_anterior', 'estado_nuevo', 'user_id', 'observacion'];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
