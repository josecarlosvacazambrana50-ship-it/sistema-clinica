@csrf
@php $sel = collect(old('especialidades', isset($medico) ? $medico->especialidades->pluck('id')->all() : [])); @endphp
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <x-input-label for="nombres" value="Nombres *" />
        <x-text-input id="nombres" name="nombres" type="text" maxlength="80" required class="block mt-1 w-full" :value="old('nombres', $medico->nombres ?? '')" />
        <x-input-error :messages="$errors->get('nombres')" class="mt-1" />
    </div>
    <div>
        <x-input-label for="apellidos" value="Apellidos *" />
        <x-text-input id="apellidos" name="apellidos" type="text" maxlength="80" required class="block mt-1 w-full" :value="old('apellidos', $medico->apellidos ?? '')" />
        <x-input-error :messages="$errors->get('apellidos')" class="mt-1" />
    </div>
    <div>
        <x-input-label for="nro_registro" value="Registro profesional *" />
        <x-text-input id="nro_registro" name="nro_registro" type="text" maxlength="30" required class="block mt-1 w-full" :value="old('nro_registro', $medico->nro_registro ?? '')" />
        <x-input-error :messages="$errors->get('nro_registro')" class="mt-1" />
    </div>
    <div>
        <x-input-label for="telefono" value="Teléfono" />
        <x-text-input id="telefono" name="telefono" type="text" maxlength="20" data-tipo="telefono" class="block mt-1 w-full" :value="old('telefono', $medico->telefono ?? '')" />
        <x-input-error :messages="$errors->get('telefono')" class="mt-1" />
    </div>
    <div class="sm:col-span-2">
        <x-input-label for="correo" value="Correo electrónico" />
        <x-text-input id="correo" name="correo" type="email" maxlength="120" class="block mt-1 w-full" :value="old('correo', $medico->correo ?? '')" />
        <x-input-error :messages="$errors->get('correo')" class="mt-1" />
    </div>
    <div class="sm:col-span-2">
        <x-input-label value="Especialidades * (una o varias)" />
        <div class="mt-1 grid grid-cols-2 gap-2" data-grupo-requerido>
            @foreach ($especialidades as $e)
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="especialidades[]" value="{{ $e->id }}" class="rounded border-gray-300" @checked($sel->contains($e->id))> {{ $e->nombre }}</label>
            @endforeach
        </div>
        <x-input-error :messages="$errors->get('especialidades')" class="mt-1" />
    </div>
</div>
<div class="mt-6 flex gap-2">
    <button class="px-4 py-2 bg-green-600 text-white rounded-md text-sm">Guardar</button>
    <a href="{{ route('medicos.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm text-gray-700">Cancelar</a>
</div>
