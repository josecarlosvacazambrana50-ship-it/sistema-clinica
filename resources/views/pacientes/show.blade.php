<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $paciente->nombre_completo }} <span class="text-sm text-gray-500">({{ $paciente->codigo }})</span></h2>
            @can('permiso', ['citas', 'crear'])
                @if ($paciente->estado === 'activo')
                    <a href="{{ route('citas.create', ['paciente_id' => $paciente->id]) }}" class="px-4 py-2 bg-[#2F5597] text-white rounded-md text-sm hover:bg-[#1F3864]">+ Nueva cita</a>
                @endif
            @endcan
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow-sm sm:rounded-lg p-4 sm:p-6">
                <dl class="grid grid-cols-1 sm:grid-cols-3 gap-x-6 gap-y-3 text-sm">
                    <div><dt class="text-gray-500">CI</dt><dd class="font-medium">{{ $paciente->ci }}</dd></div>
                    <div><dt class="text-gray-500">Fecha de nacimiento</dt><dd class="font-medium">{{ $paciente->fecha_nacimiento->format('d/m/Y') }}</dd></div>
                    <div><dt class="text-gray-500">Estado</dt><dd><x-estado :valor="$paciente->estado" /></dd></div>
                    <div><dt class="text-gray-500">Teléfono</dt><dd class="font-medium">{{ $paciente->telefono }}</dd></div>
                    <div><dt class="text-gray-500">Correo</dt><dd class="font-medium">{{ $paciente->correo ?: '—' }}</dd></div>
                    <div><dt class="text-gray-500">Dirección</dt><dd class="font-medium">{{ $paciente->direccion ?: '—' }}</dd></div>
                    <div><dt class="text-gray-500">Tipo de sangre</dt><dd class="font-medium">{{ $paciente->tipo_sangre ?: '—' }}</dd></div>
                    <div><dt class="text-gray-500">Alergias</dt><dd class="font-medium">{{ $paciente->alergias ?: '—' }}</dd></div>
                    <div><dt class="text-gray-500">Enfermedades crónicas</dt><dd class="font-medium">{{ $paciente->enfermedades_cronicas ?: '—' }}</dd></div>
                    <div class="sm:col-span-3"><dt class="text-gray-500">Contacto de emergencia</dt><dd class="font-medium">{{ $paciente->emergencia_nombre ? $paciente->emergencia_nombre . ' (' . $paciente->emergencia_parentesco . ') · ' . $paciente->emergencia_telefono : '—' }}</dd></div>
                </dl>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-4 sm:p-6">
                <h3 class="font-semibold text-[#1F3864] mb-3">Historial de citas</h3>
                @include('citas._tabla', ['citas' => $citas])
            </div>
            <a href="{{ route('pacientes.index') }}" class="text-indigo-600 hover:underline text-sm">← Volver al listado</a>
        </div>
    </div>
</x-app-layout>
