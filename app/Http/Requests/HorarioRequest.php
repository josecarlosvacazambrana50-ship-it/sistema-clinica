<?php

namespace App\Http\Requests;

use App\Models\HorarioAtencion;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class HorarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'dia_semana'        => ['required', 'integer', 'between:1,7'],
            'hora_inicio'       => ['required', 'date_format:H:i'],
            'hora_fin'          => ['required', 'date_format:H:i', 'after:hora_inicio'],
            'duracion_cita_min' => ['required', Rule::in(HorarioAtencion::DURACIONES)],
        ];
    }

    public function messages(): array
    {
        return ['hora_fin.after' => 'La hora final debe ser posterior a la hora inicial.'];
    }
}
