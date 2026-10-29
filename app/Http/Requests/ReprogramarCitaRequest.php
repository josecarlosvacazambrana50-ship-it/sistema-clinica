<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReprogramarCitaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fecha' => ['required', 'date', 'after_or_equal:today'],
            'hora_inicio' => ['required', 'date_format:H:i'],
            'observacion' => ['nullable', 'string', 'max:200'],
        ];
    }

    public function messages(): array
    {
        return [
            'fecha.after_or_equal' => 'No se pueden registrar citas en fechas pasadas.',
            'hora_inicio.required' => 'Seleccione una hora disponible.',
        ];
    }
}
