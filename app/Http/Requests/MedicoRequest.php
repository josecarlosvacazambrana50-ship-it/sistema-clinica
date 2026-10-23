<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MedicoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('medico')?->id;

        return [
            'nro_registro'      => ['required', 'string', 'max:30', Rule::unique('medicos', 'nro_registro')->ignore($id)],
            'nombres'           => ['required', 'string', 'max:80'],
            'apellidos'         => ['required', 'string', 'max:80'],
            'telefono'          => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\s\-]{6,20}$/'],
            'correo'            => ['nullable', 'email', 'max:120'],
            'especialidades'    => ['required', 'array', 'min:1'],
            'especialidades.*'  => ['integer', 'exists:especialidades,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'nro_registro.unique'     => 'Ya existe un médico con ese registro profesional.',
            'especialidades.required' => 'Seleccione al menos una especialidad.',
            'especialidades.min'      => 'Seleccione al menos una especialidad.',
        ];
    }
}
