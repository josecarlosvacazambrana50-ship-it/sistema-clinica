<?php

namespace App\Http\Requests;

use App\Models\Especialidad;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EspecialidadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        // Se valida la unicidad sobre el nombre normalizado (minúsculas y sin tildes)
        $this->merge(['nombre_normalizado' => Especialidad::normalizar((string) $this->input('nombre'))]);
    }

    public function rules(): array
    {
        $id = $this->route('especialidad')?->id;

        return [
            'nombre'             => ['required', 'string', 'max:60'],
            'nombre_normalizado' => [Rule::unique('especialidades', 'nombre_normalizado')->ignore($id)],
            'descripcion'        => ['nullable', 'string', 'max:200'],
        ];
    }

    public function messages(): array
    {
        return ['nombre_normalizado.unique' => 'Ya existe una especialidad con ese nombre.'];
    }
}
