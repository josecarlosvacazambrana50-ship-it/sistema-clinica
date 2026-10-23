<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PacienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // el acceso se controla con el middleware "permiso"
    }

    public function rules(): array
    {
        $id = $this->route('paciente')?->id;

        return [
            'ci'                    => ['required', 'string', 'max:20', 'regex:/^[0-9A-Za-z\-]+$/', Rule::unique('pacientes', 'ci')->ignore($id)],
            'nombres'               => ['required', 'string', 'max:80'],
            'apellidos'             => ['required', 'string', 'max:80'],
            'fecha_nacimiento'      => ['required', 'date', 'before_or_equal:today'],
            'sexo'                  => ['nullable', Rule::in(['M', 'F', 'O'])],
            'telefono'              => ['required', 'string', 'max:20', 'regex:/^[0-9+\s\-]{6,20}$/'],
            'correo'                => ['nullable', 'email', 'max:120'],
            'direccion'             => ['nullable', 'string', 'max:200'],
            'tipo_sangre'           => ['nullable', Rule::in(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])],
            'alergias'              => ['nullable', 'string', 'max:1000'],
            'enfermedades_cronicas' => ['nullable', 'string', 'max:1000'],
            'medicamentos'          => ['nullable', 'string', 'max:1000'],
            'antecedentes'          => ['nullable', 'string', 'max:1000'],
            'emergencia_nombre'     => ['nullable', 'string', 'max:100'],
            'emergencia_parentesco' => ['nullable', 'string', 'max:40'],
            'emergencia_telefono'   => ['nullable', 'string', 'max:20'],
        ];
    }

    public function messages(): array
    {
        return [
            'ci.unique'                       => 'Ya existe un paciente registrado con ese CI.',
            'ci.regex'                        => 'El CI solo puede contener letras, números y guiones.',
            'fecha_nacimiento.before_or_equal' => 'La fecha de nacimiento no puede ser futura.',
            'telefono.regex'                  => 'Ingrese un teléfono válido (solo números, +, espacios o guiones).',
        ];
    }
}
