<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PerfilPacienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->rol?->nombre === 'Paciente'
            && $this->user()->tienePermiso('perfil', 'editar');
    }

    public function rules(): array
    {
        $pacienteId = $this->user()->paciente_id;

        return [
            'nombres' => ['required', 'string', 'max:80'],
            'apellidos' => ['required', 'string', 'max:80'],
            'fecha_nacimiento' => ['required', 'date', 'before:today'],
            'sexo' => ['nullable', Rule::in(['M', 'F', 'O'])],
            'telefono' => ['required', 'string', 'max:20', 'regex:/^[0-9+\s-]{6,20}$/'],
            'correo' => ['nullable', 'email', 'max:120', Rule::unique('pacientes', 'correo')->ignore($pacienteId), Rule::unique('users', 'email')->ignore($this->user()->id)],
            'direccion' => ['nullable', 'string', 'max:200'],
            'tipo_sangre' => ['nullable', Rule::in(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])],
            'alergias' => ['nullable', 'string'],
            'enfermedades_cronicas' => ['nullable', 'string'],
            'medicamentos' => ['nullable', 'string'],
            'antecedentes' => ['nullable', 'string'],
            'emergencia_nombre' => ['nullable', 'string', 'max:100'],
            'emergencia_parentesco' => ['nullable', 'string', 'max:40'],
            'emergencia_telefono' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\s-]{6,20}$/'],
        ];
    }
}
