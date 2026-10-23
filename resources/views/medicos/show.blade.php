<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $medico->nombre_completo }}</h2></x-slot>
    <div class="py-8"><div class="max-w-3xl mx-auto sm:px-6 lg:px-8"><div class="bg-white shadow-sm sm:rounded-lg p-4 sm:p-6 text-sm space-y-2">
        <p><b>Código:</b> {{ $medico->codigo }} · <b>Registro:</b> {{ $medico->nro_registro }}</p>
        <p><b>Especialidades:</b> {{ $medico->especialidades->pluck('nombre')->join(', ') }}</p>
        <p><b>Contacto:</b> {{ $medico->telefono }} {{ $medico->correo }}</p>
        <a href="{{ route('horarios.index', $medico) }}" class="text-indigo-600 hover:underline">Ver horarios de atención</a>
    </div></div></div>
</x-app-layout>
