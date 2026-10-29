<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CambiarEstadoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'estado' => ['required', 'in:confirmada,atendida,inasistencia'],
            'observacion' => ['nullable', 'string', 'max:200'],
        ];
    }
}
