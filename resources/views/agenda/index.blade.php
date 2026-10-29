<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Agenda médica</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto space-y-6 sm:px-6 lg:px-8">
            <section class="rounded-lg bg-white p-4 shadow-sm sm:p-6">
                <form method="GET" class="grid grid-cols-1 gap-3 sm:grid-cols-4">
                    @unless (Auth::user()->esMedico())
                        <div>
                            <label for="medico_id" class="mb-1 block text-sm font-medium text-gray-700">Médico</label>
                            <select id="medico_id" name="medico_id" class="w-full rounded-md border-gray-300 text-sm">
                                @foreach ($medicos as $opcionMedico)
                                    <option value="{{ $opcionMedico->id }}" @selected($filtros['medico_id'] == $opcionMedico->id)>{{ $opcionMedico->nombre_completo }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endunless
                    <div>
                        <label for="fecha" class="mb-1 block text-sm font-medium text-gray-700">Fecha</label>
                        <input id="fecha" type="date" name="fecha" value="{{ $filtros['fecha'] }}" class="w-full rounded-md border-gray-300 text-sm">
                    </div>
                    <div>
                        <label for="estado" class="mb-1 block text-sm font-medium text-gray-700">Estado</label>
                        <select id="estado" name="estado" class="w-full rounded-md border-gray-300 text-sm">
                            <option value="">Todos los estados</option>
                            @foreach (\App\Models\Cita::ESTADOS as $valor => $etiqueta)
                                <option value="{{ $valor }}" @selected($filtros['estado'] === $valor)>{{ $etiqueta }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end gap-2">
                        <button class="rounded-md bg-[#2F5597] px-4 py-2 text-sm text-white">Filtrar</button>
                        <a href="{{ route('agenda.index') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm text-gray-700">Limpiar</a>
                    </div>
                </form>
            </section>

            @if ($medico)
                <section class="rounded-lg bg-white p-4 shadow-sm sm:p-6">
                    <h3 class="font-semibold text-[#1F3864]">Resumen del día · {{ $medico->nombre_completo }}</h3>
                    <p class="mt-1 text-sm text-gray-500">{{ \Carbon\Carbon::parse($filtros['fecha'])->format('d/m/Y') }}</p>
                    <dl class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-5">
                        @foreach (\App\Models\Cita::ESTADOS as $estado => $nombre)
                            <div class="rounded-md bg-gray-50 p-3 text-center">
                                <dt class="text-xs text-gray-500">{{ $nombre }}</dt>
                                <dd class="text-lg font-semibold text-gray-800">{{ $totales[$estado] }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </section>
            @endif

            <section class="rounded-lg bg-white p-4 shadow-sm sm:p-6">
                <h3 class="mb-3 font-semibold text-[#1F3864]">Citas programadas</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="border-b text-gray-600">
                            <tr>
                                <th class="py-2">Hora</th><th>Código</th><th>Paciente</th><th>Especialidad</th><th>Estado</th>
                                @if ($mostrarMotivo)<th>Motivo</th>@endif
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($citas as $cita)
                                <tr class="border-b">
                                    <td class="py-2">{{ $cita->hora_inicio_corta }}–{{ $cita->hora_fin_corta }}</td>
                                    <td>{{ $cita->codigo }}</td>
                                    <td>{{ $cita->paciente->nombre_completo }}</td>
                                    <td>{{ $cita->especialidad->nombre }}</td>
                                    <td><x-estado :valor="$cita->estado" /></td>
                                    @if ($mostrarMotivo)<td>{{ $cita->motivo ?: '—' }}</td>@endif
                                    <td><a href="{{ route('citas.show', $cita) }}" class="text-indigo-600 hover:underline">Ver</a></td>
                                </tr>
                            @empty
                                <tr><td colspan="{{ $mostrarMotivo ? 7 : 6 }}" class="py-6 text-center text-gray-500">Sin citas programadas</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            @if ($medico)
                <section class="rounded-lg bg-white p-4 shadow-sm sm:p-6">
                    <h3 class="mb-3 font-semibold text-[#1F3864]">Horas libres</h3>
                    <div class="flex flex-wrap gap-2">
                        @forelse (collect($bloques)->where('libre', true) as $bloque)
                            <span class="rounded-md border border-green-600 px-3 py-1 text-sm text-green-700">{{ $bloque['inicio'] }}–{{ $bloque['fin'] }}</span>
                        @empty
                            <p class="text-sm text-gray-500">No hay horas libres para esta fecha.</p>
                        @endforelse
                    </div>
                </section>
            @endif
        </div>
    </div>
</x-app-layout>
