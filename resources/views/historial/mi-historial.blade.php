<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Mi historial médico</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-[#1F3864] text-lg mb-4">Citas atendidas y recetas</h3>

                @if($citasAtendidas->isEmpty())
                    <p class="text-sm text-gray-500">No tienes atenciones médicas registradas todavía.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50 text-gray-700">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold">Código</th>
                                    <th class="px-4 py-3 text-left font-semibold">Fecha</th>
                                    <th class="px-4 py-3 text-left font-semibold">Médico</th>
                                    <th class="px-4 py-3 text-left font-semibold">Especialidad</th>
                                    <th class="px-4 py-3 text-left font-semibold">Diagnóstico y Receta</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @foreach ($citasAtendidas as $cita)
                                    <tr>
                                        <td class="px-4 py-3 font-medium text-gray-900">{{ $cita->codigo }}</td>
                                        <td class="px-4 py-3 text-gray-600">{{ $cita->fecha->format('d/m/Y') }}</td>
                                        <td class="px-4 py-3 text-gray-900">Dr(a). {{ $cita->medico->apellidos ?? '' }} {{ $cita->medico->nombres ?? '' }}</td>
                                        <td class="px-4 py-3 text-gray-600">{{ $cita->especialidad->nombre ?? '-' }}</td>
                                        <td class="px-4 py-3 text-gray-700">
                                            @if($cita->diagnostico || $cita->receta || $cita->observaciones)
                                                <div class="p-2 bg-gray-50 rounded border text-xs space-y-1">
                                                    @if($cita->diagnostico)
                                                        <p><strong>Diagnóstico:</strong> {{ $cita->diagnostico }}</p>
                                                    @endif
                                                    @if($cita->receta)
                                                        <p><strong>Receta:</strong> {{ $cita->receta }}</p>
                                                    @endif
                                                    @if($cita->observaciones)
                                                        <p><strong>Observaciones:</strong> {{ $cita->observaciones }}</p>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-gray-400 italic">Sin diagnóstico registrado</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $citasAtendidas->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>