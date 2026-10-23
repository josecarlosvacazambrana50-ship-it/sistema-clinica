@csrf
@php $rolSel = old('rol_id', $usuario->rol_id ?? ''); @endphp
<div class="space-y-4" id="form-usuario">
    <div>
        <x-input-label for="email" value="Correo electrónico *" />
        <x-text-input id="email" name="email" type="email" maxlength="100" required class="block mt-1 w-full" :value="old('email', $usuario->email ?? '')" />
        <x-input-error :messages="$errors->get('email')" class="mt-1" />
    </div>
    <div>
        <x-input-label for="password" :value="isset($usuario) ? 'Nueva contraseña (dejar vacío para no cambiarla)' : 'Contraseña *'" />
        <x-text-input id="password" name="password" type="password" minlength="8" :required="! isset($usuario)" autocomplete="new-password" class="block mt-1 w-full" />
        <x-input-error :messages="$errors->get('password')" class="mt-1" />
    </div>
    <div>
        <x-input-label for="rol_id" value="Rol *" />
        <select id="rol_id" name="rol_id" required class="block mt-1 w-full rounded-md border-gray-300 text-sm">
            <option value="">Seleccione...</option>
            @foreach ($roles as $r)<option value="{{ $r->id }}" data-nombre="{{ $r->nombre }}" @selected($rolSel == $r->id)>{{ $r->nombre }}</option>@endforeach
        </select>
        <x-input-error :messages="$errors->get('rol_id')" class="mt-1" />
    </div>
    <div id="campo-medico" class="hidden">
        <x-input-label for="medico_id" value="Médico de la cuenta *" />
        <select id="medico_id" name="medico_id" class="block mt-1 w-full rounded-md border-gray-300 text-sm">
            <option value="">Seleccione...</option>
            @foreach ($medicos as $m)<option value="{{ $m->id }}" @selected(old('medico_id', $usuario->medico_id ?? '') == $m->id)>{{ $m->nombre_completo }}</option>@endforeach
        </select>
        <x-input-error :messages="$errors->get('medico_id')" class="mt-1" />
    </div>
    <div id="campo-paciente" class="hidden">
        <x-input-label for="paciente_id" value="Paciente de la cuenta *" />
        <select id="paciente_id" name="paciente_id" class="block mt-1 w-full rounded-md border-gray-300 text-sm">
            <option value="">Seleccione...</option>
            @foreach ($pacientes as $p)<option value="{{ $p->id }}" @selected(old('paciente_id', $usuario->paciente_id ?? '') == $p->id)>{{ $p->nombre_completo }} (CI {{ $p->ci }})</option>@endforeach
        </select>
        <x-input-error :messages="$errors->get('paciente_id')" class="mt-1" />
    </div>
    <div class="flex gap-2">
        <button class="px-4 py-2 bg-green-600 text-white rounded-md text-sm">Guardar</button>
        <a href="{{ route('usuarios.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm text-gray-700">Cancelar</a>
    </div>
</div>
