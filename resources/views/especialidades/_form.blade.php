@csrf
<div class="space-y-4">
    <div>
        <x-input-label for="nombre" value="Nombre *" />
        <x-text-input id="nombre" name="nombre" type="text" maxlength="60" required class="block mt-1 w-full" :value="old('nombre', $especialidad->nombre ?? '')" />
        <x-input-error :messages="$errors->get('nombre')" class="mt-1" />
        <x-input-error :messages="$errors->get('nombre_normalizado')" class="mt-1" />
    </div>
    <div>
        <x-input-label for="descripcion" value="Descripción (opcional)" />
        <x-text-input id="descripcion" name="descripcion" type="text" maxlength="200" class="block mt-1 w-full" :value="old('descripcion', $especialidad->descripcion ?? '')" />
        <x-input-error :messages="$errors->get('descripcion')" class="mt-1" />
    </div>
    <div class="flex gap-2">
        <button class="px-4 py-2 bg-green-600 text-white rounded-md text-sm">Guardar</button>
        <a href="{{ route('especialidades.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm text-gray-700">Cancelar</a>
    </div>
</div>
