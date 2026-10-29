<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Reportes</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto space-y-6 sm:px-6 lg:px-8">
            @unless (isset($tipoActual))
                <section class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($tipos as $tipo => $titulo)
                        <a href="{{ route('reportes.mostrar', $tipo) }}" class="rounded-lg bg-white p-5 shadow-sm transition hover:shadow-md">
                            <h3 class="font-semibold text-[#1F3864]">{{ $titulo }}</h3>
                            <p class="mt-2 text-sm text-gray-500">Consultar información y totales.</p>
                        </a>
                    @endforeach
                </section>
            @else
                <section class="rounded-lg bg-white p-4 shadow-sm sm:p-6">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <h3 class="font-semibold text-[#1F3864]">{{ $reporte['titulo'] }}</h3>
                        <a href="{{ route('reportes.index') }}" class="text-sm text-indigo-600 hover:underline">Todos los reportes</a>
                    </div>
                    <form method="GET" class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-3">
                        <div>
                            <label for="fecha_desde" class="mb-1 block text-sm font-medium text-gray-700">Desde</label>
                            <input id="fecha_desde" type="date" name="fecha_desde" value="{{ $filtros['fecha_desde'] ?? '' }}" class="w-full rounded-md border-gray-300 text-sm">
                        </div>
                        <div>
                            <label for="fecha_hasta" class="mb-1 block text-sm font-medium text-gray-700">Hasta</label>
                            <input id="fecha_hasta" type="date" name="fecha_hasta" value="{{ $filtros['fecha_hasta'] ?? '' }}" class="w-full rounded-md border-gray-300 text-sm">
                        </div>
                        <div class="flex items-end gap-2">
                            <button class="rounded-md bg-[#2F5597] px-4 py-2 text-sm text-white">Aplicar filtros</button>
                            <a href="{{ route('reportes.mostrar', $tipoActual) }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm text-gray-700">Limpiar</a>
                        </div>
                    </form>

                    <dl class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-6">
                        <div class="rounded-md bg-gray-50 p-3 text-center">
                            <dt class="text-xs text-gray-500">Total</dt><dd class="text-lg font-semibold">{{ $reporte['totalCitas'] }}</dd>
                        </div>
                        @foreach (\App\Models\Cita::ESTADOS as $estado => $nombre)
                            <div class="rounded-md bg-gray-50 p-3 text-center">
                                <dt class="text-xs text-gray-500">{{ $nombre }}</dt><dd class="text-lg font-semibold">{{ $reporte['totalesPorEstado'][$estado] }}</dd>
                            </div>
                        @endforeach
                    </dl>

                    <div class="mt-5 overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="border-b text-gray-600"><tr><th class="py-2">Grupo</th><th>Estado</th><th>Total</th></tr></thead>
                            <tbody>
                                @forelse ($reporte['filas'] as $fila)
                                    <tr class="border-b">
                                        <td class="py-2">{{ $fila->grupo }}</td>
                                        <td>{{ \App\Models\Cita::ESTADOS[$fila->estado] ?? '—' }}</td>
                                        <td>{{ $fila->total }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="py-6 text-center text-gray-500">No hay resultados para los filtros seleccionados.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            @endunless
        </div>
    </div>
</x-app-layout>
