<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $paciente ? 'Historial de ' . $paciente->nombre_completo : 'Mi historial de citas' }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto space-y-6 sm:px-6 lg:px-8">
            @if (!$paciente)
                <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                    Tu cuenta aún no está vinculada a un perfil de paciente. Contacta a recepción.
                </div>
            @endif
            <section class="rounded-lg bg-white p-4 shadow-sm sm:p-6">
                <form method="GET" class="grid grid-cols-1 gap-3 sm:grid-cols-4">
                    <div>
                        <label for="estado" class="mb-1 block text-sm font-medium text-gray-700">Estado</label>
                        <select id="estado" name="estado" class="w-full rounded-md border-gray-300 text-sm">
                            <option value="">Todos los estados</option>
                            @foreach (\App\Models\Cita::ESTADOS as $valor => $nombre)
                                <option value="{{ $valor }}" @selected(($filtros['estado'] ?? '') === $valor)>{{ $nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="fecha_desde" class="mb-1 block text-sm font-medium text-gray-700">Desde</label>
                        <input id="fecha_desde" type="date" name="fecha_desde" value="{{ $filtros['fecha_desde'] ?? '' }}" class="w-full rounded-md border-gray-300 text-sm">
                    </div>
                    <div>
                        <label for="fecha_hasta" class="mb-1 block text-sm font-medium text-gray-700">Hasta</label>
                        <input id="fecha_hasta" type="date" name="fecha_hasta" value="{{ $filtros['fecha_hasta'] ?? '' }}" class="w-full rounded-md border-gray-300 text-sm">
                    </div>
                    <div class="flex items-end gap-2">
                        <button class="rounded-md bg-[#2F5597] px-4 py-2 text-sm text-white">Filtrar</button>
                        <a href="{{ $paciente ? route('pacientes.historial', $paciente) : route('mi.historial') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm text-gray-700">Limpiar</a>
                    </div>
                </form>
            </section>

            <section class="rounded-lg bg-white p-4 shadow-sm sm:p-6">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="border-b text-gray-600">
                            <tr><th class="py-2">Fecha y hora</th><th>Médico</th><th>Especialidad</th><th>Estado</th><th>Registrada</th><th></th></tr>
                        </thead>
                        <tbody>
                            @forelse ($citas as $cita)
                                <tr class="border-b">
                                    <td class="py-2">{{ $cita->fecha->format('d/m/Y') }} · {{ $cita->hora_inicio_corta }}–{{ $cita->hora_fin_corta }}</td>
                                    <td>{{ $cita->medico->nombre_completo }}</td>
                                    <td>{{ $cita->especialidad->nombre }}</td>
                                    <td><x-estado :valor="$cita->estado" /></td>
                                    <td>{{ $cita->created_at->format('d/m/Y H:i') }}</td>
                                    <td><a href="{{ route('citas.show', $cita) }}" class="text-indigo-600 hover:underline">Detalle e historial</a></td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="py-6 text-center text-gray-500">No hay citas para mostrar.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $citas->links() }}</div>
            </section>
        </div>
    </div>
</x-app-layout>
