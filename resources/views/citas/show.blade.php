<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Cita {{ $cita->codigo }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow-sm sm:rounded-lg p-4 sm:p-6">
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 text-sm">
                    <div><dt class="text-gray-500">Estado</dt><dd><x-estado :valor="$cita->estado" /></dd></div>
                    <div><dt class="text-gray-500">Fecha y hora</dt><dd class="font-medium">{{ $cita->fecha->format('d/m/Y') }} · {{ $cita->hora_inicio_corta }} – {{ $cita->hora_fin_corta }}</dd></div>
                    <div><dt class="text-gray-500">Paciente</dt><dd class="font-medium">{{ $cita->paciente->nombre_completo }} (CI {{ $cita->paciente->ci }})</dd></div>
                    <div><dt class="text-gray-500">Médico</dt><dd class="font-medium">Dr(a). {{ $cita->medico->nombre_completo }}</dd></div>
                    <div><dt class="text-gray-500">Especialidad</dt><dd class="font-medium">{{ $cita->especialidad->nombre }}</dd></div>
                    <div><dt class="text-gray-500">Motivo</dt><dd class="font-medium">{{ $cita->motivo ?: '—' }}</dd></div>
                    <div><dt class="text-gray-500">Registrada por</dt><dd class="font-medium">{{ $cita->registradaPor->email }} el {{ $cita->created_at->format('d/m/Y H:i') }}</dd></div>
                </dl>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-4 sm:p-6">
                <h3 class="font-semibold text-[#1F3864] mb-3">Historial de estados</h3>
                <ul class="text-sm divide-y">
                    @foreach ($cita->historial as $h)
                        <li class="py-2 flex justify-between"><span>{{ ucfirst($h->estado_nuevo) }} — {{ $h->observacion }}</span><span class="text-gray-500">{{ $h->created_at->format('d/m/Y H:i') }} · {{ $h->usuario->email }}</span></li>
                    @endforeach
                </ul>
            </div>
            <a href="{{ route('citas.index') }}" class="text-indigo-600 hover:underline text-sm">← Volver al listado</a>
        </div>
    </div>
</x-app-layout>
