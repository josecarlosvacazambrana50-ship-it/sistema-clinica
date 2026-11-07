<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <!-- TÍTULO Y BOTÓN FORZADO -->
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid #f3f4f6; padding-bottom: 1rem;">
                    <h1 style="font-size: 1.5rem; font-weight: bold; color: #1f2937;">
                        {{ Auth::user()->rol_id == 3 ? 'Mis citas' : 'Citas médicas' }}
                    </h1>

                    <a href="{{ route('citas.create') }}" style="background-color: #4f46e5; color: white; padding: 0.5rem 1rem; border-radius: 0.375rem; font-weight: 600; font-size: 0.75rem; text-transform: uppercase; text-decoration: none; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);">
                        + Nueva cita médica
                    </a>
                </div>

                <!-- Filtros de búsqueda -->
                <form method="GET" action="{{ route('citas.index') }}" class="mb-6 grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
                    <div>
                        <x-input-label for="fecha" :value="__('Fecha')" />
                        <x-text-input id="fecha" type="date" name="fecha" value="{{ $filtros['fecha'] ?? '' }}" class="block mt-1 w-full" />
                    </div>

                    <div>
                        <x-input-label for="medico_id" :value="__('Médico')" />
                        <select id="medico_id" name="medico_id" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full text-sm">
                            <option value="">Todos los médicos</option>
                            @foreach($medicos as $medico)
                                <option value="{{ $medico->id }}" {{ (isset($filtros['medico_id']) && $filtros['medico_id'] == $medico->id) ? 'selected' : '' }}>
                                    Dr(a). {{ $medico->apellidos }}, {{ $medico->nombres }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <x-input-label for="estado" :value="__('Estado')" />
                        <select id="estado" name="estado" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full text-sm">
                            <option value="">Todos los estados</option>
                            <option value="programada" {{ (isset($filtros['estado']) && $filtros['estado'] == 'programada') ? 'selected' : '' }}>Programada</option>
                            <option value="confirmada" {{ (isset($filtros['estado']) && $filtros['estado'] == 'confirmada') ? 'selected' : '' }}>Confirmada</option>
                            <option value="atendida" {{ (isset($filtros['estado']) && $filtros['estado'] == 'atendida') ? 'selected' : '' }}>Atendida</option>
                            <option value="cancelada" {{ (isset($filtros['estado']) && $filtros['estado'] == 'cancelada') ? 'selected' : '' }}>Cancelada</option>
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <x-primary-button class="h-10">
                            {{ __('Filtrar') }}
                        </x-primary-button>
                        <a href="{{ route('citas.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                            {{ __('Limpiar') }}
                        </a>
                    </div>
                </form>

                <!-- Listado de Citas -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hora</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paciente</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Médico</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Especialidad</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pago</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($citas as $cita)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $cita->codigo }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ optional($cita->fecha)->format('d/m/Y') ?? $cita->fecha }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $cita->hora_inicio_corta ?? substr($cita->hora_inicio, 0, 5) }} - {{ $cita->hora_fin_corta ?? substr($cita->hora_fin, 0, 5) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $cita->paciente->nombres ?? 'N/D' }} {{ $cita->paciente->apellidos ?? '' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Dr(a). {{ $cita->medico->nombres ?? 'N/D' }} {{ $cita->medico->apellidos ?? '' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $cita->especialidad->nombre ?? 'N/D' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ ucfirst($cita->estado) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($cita->metodo_pago)
                                            <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                ✓ Pagado ({{ $cita->metodo_pago }})
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-600">
                                                Registrado
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('citas.show', $cita->id) }}" class="text-indigo-600 hover:text-indigo-900 font-semibold">Ver</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-6 py-4 text-center text-sm text-gray-500">
                                        No hay citas registradas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="mt-4">
                    {{ $citas->links() }}
                </div>

            </div>
        </div>
    </div>
</x-app-layout>