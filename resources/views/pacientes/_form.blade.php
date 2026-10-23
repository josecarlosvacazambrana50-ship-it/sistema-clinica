@csrf
<h3 class="font-semibold text-[#1F3864] mb-3">Datos personales y de contacto</h3>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <x-input-label for="ci" value="CI *" />
        <x-text-input id="ci" name="ci" type="text" maxlength="20" required data-tipo="ci" class="block mt-1 w-full" :value="old('ci', isset($paciente) ? ($paciente->ci) : '')" />
        <x-input-error :messages="$errors->get('ci')" class="mt-1" />
    </div>
    <div>
        <x-input-label for="nombres" value="Nombres *" />
        <x-text-input id="nombres" name="nombres" type="text" maxlength="80" required class="block mt-1 w-full" :value="old('nombres', isset($paciente) ? ($paciente->nombres) : '')" />
        <x-input-error :messages="$errors->get('nombres')" class="mt-1" />
    </div>
    <div>
        <x-input-label for="apellidos" value="Apellidos *" />
        <x-text-input id="apellidos" name="apellidos" type="text" maxlength="80" required class="block mt-1 w-full" :value="old('apellidos', isset($paciente) ? ($paciente->apellidos) : '')" />
        <x-input-error :messages="$errors->get('apellidos')" class="mt-1" />
    </div>
    <div>
        <x-input-label for="fecha_nacimiento" value="Fecha de nacimiento *" />
        <x-text-input id="fecha_nacimiento" name="fecha_nacimiento" type="date" required max="{{ today()->toDateString() }}" class="block mt-1 w-full" :value="old('fecha_nacimiento', isset($paciente) ? ($paciente->fecha_nacimiento?->format('Y-m-d')) : '')" />
        <x-input-error :messages="$errors->get('fecha_nacimiento')" class="mt-1" />
    </div>
    <div>
        <x-input-label for="telefono" value="Teléfono *" />
        <x-text-input id="telefono" name="telefono" type="text" maxlength="20" required data-tipo="telefono" class="block mt-1 w-full" :value="old('telefono', isset($paciente) ? ($paciente->telefono) : '')" />
        <x-input-error :messages="$errors->get('telefono')" class="mt-1" />
    </div>
    <div>
        <x-input-label for="correo" value="Correo electrónico" />
        <x-text-input id="correo" name="correo" type="email" maxlength="120" class="block mt-1 w-full" :value="old('correo', isset($paciente) ? ($paciente->correo) : '')" />
        <x-input-error :messages="$errors->get('correo')" class="mt-1" />
    </div>
    <div class="sm:col-span-2">
        <x-input-label for="direccion" value="Dirección" />
        <x-text-input id="direccion" name="direccion" type="text" maxlength="200" class="block mt-1 w-full" :value="old('direccion', isset($paciente) ? ($paciente->direccion) : '')" />
        <x-input-error :messages="$errors->get('direccion')" class="mt-1" />
    </div>
    <div>
        <x-input-label for="sexo" value="Sexo" />
        @php $sx = old('sexo', $paciente->sexo ?? ''); @endphp
        <select id="sexo" name="sexo" class="block mt-1 w-full rounded-md border-gray-300 text-sm">
            <option value="">—</option>
            <option value="F" @selected($sx === 'F')>Femenino</option><option value="M" @selected($sx === 'M')>Masculino</option><option value="O" @selected($sx === 'O')>Otro</option>
        </select>
    </div>
</div>

<h3 class="font-semibold text-[#1F3864] mt-8 mb-3">Información médica básica</h3>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <x-input-label for="tipo_sangre" value="Tipo de sangre" />
        @php $ts = old('tipo_sangre', $paciente->tipo_sangre ?? ''); @endphp
        <select id="tipo_sangre" name="tipo_sangre" class="block mt-1 w-full rounded-md border-gray-300 text-sm">
            <option value="">—</option>
            @foreach (['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $t)<option value="{{ $t }}" @selected($ts === $t)>{{ $t }}</option>@endforeach
        </select>
    </div>
    <div>
        <x-input-label for="alergias" value="Alergias" />
        <textarea id="alergias" name="alergias" rows="2" maxlength="1000" class="block mt-1 w-full rounded-md border-gray-300 text-sm">{{ old('alergias', $paciente->alergias ?? '') }}</textarea>
    </div>
    <div>
        <x-input-label for="enfermedades_cronicas" value="Enfermedades crónicas" />
        <textarea id="enfermedades_cronicas" name="enfermedades_cronicas" rows="2" maxlength="1000" class="block mt-1 w-full rounded-md border-gray-300 text-sm">{{ old('enfermedades_cronicas', $paciente->enfermedades_cronicas ?? '') }}</textarea>
    </div>
    <div>
        <x-input-label for="medicamentos" value="Medicamentos" />
        <textarea id="medicamentos" name="medicamentos" rows="2" maxlength="1000" class="block mt-1 w-full rounded-md border-gray-300 text-sm">{{ old('medicamentos', $paciente->medicamentos ?? '') }}</textarea>
    </div>
    <div>
        <x-input-label for="antecedentes" value="Antecedentes relevantes" />
        <textarea id="antecedentes" name="antecedentes" rows="2" maxlength="1000" class="block mt-1 w-full rounded-md border-gray-300 text-sm">{{ old('antecedentes', $paciente->antecedentes ?? '') }}</textarea>
    </div>
</div>

<h3 class="font-semibold text-[#1F3864] mt-8 mb-3">Contacto de emergencia</h3>
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <div>
        <x-input-label for="emergencia_nombre" value="Nombre" />
        <x-text-input id="emergencia_nombre" name="emergencia_nombre" type="text" maxlength="100" class="block mt-1 w-full" :value="old('emergencia_nombre', $paciente->emergencia_nombre ?? '')" />
        <x-input-error :messages="$errors->get('emergencia_nombre')" class="mt-1" />
    </div>
    <div>
        <x-input-label for="emergencia_parentesco" value="Parentesco" />
        <x-text-input id="emergencia_parentesco" name="emergencia_parentesco" type="text" maxlength="40" class="block mt-1 w-full" :value="old('emergencia_parentesco', $paciente->emergencia_parentesco ?? '')" />
        <x-input-error :messages="$errors->get('emergencia_parentesco')" class="mt-1" />
    </div>
    <div>
        <x-input-label for="emergencia_telefono" value="Teléfono" />
        <x-text-input id="emergencia_telefono" name="emergencia_telefono" type="text" maxlength="20" data-tipo="telefono" class="block mt-1 w-full" :value="old('emergencia_telefono', $paciente->emergencia_telefono ?? '')" />
        <x-input-error :messages="$errors->get('emergencia_telefono')" class="mt-1" />
    </div>
</div>

<div class="mt-6 flex gap-2">
    <button class="px-4 py-2 bg-green-600 text-white rounded-md text-sm">Guardar</button>
    <a href="{{ route('pacientes.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm text-gray-700">Cancelar</a>
</div>
