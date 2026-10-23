<?php

namespace App\Http\Requests;

use App\Models\Rol;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $usuario = $this->route('usuario');
        $rol = Rol::find($this->input('rol_id'))?->nombre;

        return [
            'email'       => ['required', 'email', 'max:100', Rule::unique('users', 'email')->ignore($usuario?->id)],
            'password'    => [$usuario ? 'nullable' : 'required', Password::min(8)],
            'rol_id'      => ['required', 'exists:rols,id'],
            'medico_id'   => [$rol === 'Médico' ? 'required' : 'nullable', 'exists:medicos,id', Rule::unique('users', 'medico_id')->ignore($usuario?->id)],
            'paciente_id' => [$rol === 'Paciente' ? 'required' : 'nullable', 'exists:pacientes,id', Rule::unique('users', 'paciente_id')->ignore($usuario?->id)],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique'         => 'Ya existe un usuario con ese correo.',
            'password.min'         => 'La contraseña debe tener al menos 8 caracteres.',
            'medico_id.required'   => 'Seleccione el médico al que pertenece la cuenta.',
            'medico_id.unique'     => 'Ese médico ya tiene una cuenta.',
            'paciente_id.required' => 'Seleccione el paciente al que pertenece la cuenta.',
            'paciente_id.unique'   => 'Ese paciente ya tiene una cuenta.',
        ];
    }
}
