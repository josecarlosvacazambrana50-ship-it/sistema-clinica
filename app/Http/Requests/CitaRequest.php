<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CitaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'especialidad_id' => ['required', 'exists:especialidades,id'],
            'medico_id'       => ['required', 'exists:medicos,id'],
            'fecha'           => ['required', 'date', 'after_or_equal:today'],
            'hora_inicio'     => ['required', 'date_format:H:i'],
            'hora_fin'        => ['required', 'date_format:H:i', 'after:hora_inicio'],
            'motivo'          => ['nullable', 'string', 'max:200'],
        ];

        // Si es un paciente registrado por la web (rol_id == 3), el paciente_id llega por input oculto
        if (auth()->user() && auth()->user()->rol_id == 3) {
            $rules['paciente_id'] = ['nullable', 'exists:pacientes,id'];
        } else {
            $rules['paciente_id'] = ['required', 'exists:pacientes,id'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'fecha.after_or_equal' => 'No se pueden registrar citas en fechas pasadas.',
            'hora_inicio.required' => 'Seleccione una hora disponible.',
        ];
    }
}
