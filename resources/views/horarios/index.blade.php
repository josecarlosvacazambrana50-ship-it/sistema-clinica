<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Horarios de atención — {{ $medico->nombre_completo ?? $medico->nombres }}
        </h2>
    </x-slot>

    @php
        $usuario = auth()->user();
        $esAdmin = ($usuario->rol_id == 1 || optional($usuario->rol)->nombre === 'Administrador');
    @endphp

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900">
                
                <!-- Mensajes de éxito -->
                @if (session('success'))
                    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                <h3 class="text-lg font-medium text-gray-900 mb-4">Listado de Horarios</h3>

                <!-- Tabla de Horarios -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 mb-6">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Día</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hora inicial</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hora final</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duración de cita</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                @if($esAdmin)
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($medico->horarios as $horario)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @php
                                            $diasMap = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'];
                                            $valorDia = $horario->dia_semana ?? $horario->dia;
                                        @endphp
                                        {{ $diasMap[$valorDia] ?? $valorDia ?? 'N/D' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $horario->hora_inicio ?? $horario->hora_inicial ?? 'N/D' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $horario->hora_fin ?? $horario->hora_final ?? 'N/D' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $horario->duracion_cita_min ?? $horario->duracion ?? '30' }} min</td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="px-2 py-1 text-xs rounded {{ ($horario->estado === 'activo') ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ ucfirst($horario->estado ?? 'inactivo') }}
                                        </span>
                                    </td>

                                    @if($esAdmin)
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end items-center space-x-3">
                                                <!-- Botón Activar / Desactivar -->
                                                <form action="{{ route('horarios.estado', [$medico, $horario->id]) }}" method="POST">
                                                    @csrf 
                                                    @method('PATCH')
                                                    <button type="submit" class="text-sm {{ ($horario->estado === 'activo') ? 'text-yellow-600 hover:text-yellow-900 font-medium' : 'text-green-600 hover:text-green-900 font-medium' }}">
                                                        {{ ($horario->estado === 'activo') ? 'Desactivar' : 'Activar' }}
                                                    </button>
                                                </form>

                                                <!-- Botón de Eliminar -->
                                                <form action="{{ route('horarios.destroy', [$medico, $horario->id]) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este horario?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900 text-sm font-medium">Eliminar</button>
                                                </form>
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $esAdmin ? 6 : 5 }}" class="px-6 py-4 text-center text-sm text-gray-500">No hay horarios registrados para este médico.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Formulario para Agregar Horario (Solo visible para Administradores) -->
                @if($esAdmin)
                    <div class="mt-8 bg-gray-50 p-6 rounded-lg border">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Agregar horario</h3>
                        <form action="{{ route('horarios.store', $medico) }}" method="POST" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                            @csrf
                            <div>
                                <label for="dia" class="block font-medium text-sm text-gray-700">Día *</label>
                                <select name="dia" id="dia" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    <option value="">Seleccione</option>
                                    <option value="1">Lunes</option>
                                    <option value="2">Martes</option>
                                    <option value="3">Miércoles</option>
                                    <option value="4">Jueves</option>
                                    <option value="5">Viernes</option>
                                    <option value="6">Sábado</option>
                                    <option value="7">Domingo</option>
                                </select>
                            </div>
                            <div>
                                <label for="hora_inicial" class="block font-medium text-sm text-gray-700">Hora inicial *</label>
                                <input type="time" name="hora_inicial" id="hora_inicial" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            </div>
                            <div>
                                <label for="hora_final" class="block font-medium text-sm text-gray-700">Hora final *</label>
                                <input type="time" name="hora_final" id="hora_final" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            </div>
                            <div>
                                <label for="duracion" class="block font-medium text-sm text-gray-700">Duración (min) *</label>
                                <select name="duracion" id="duracion" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    <option value="15">15 min</option>
                                    <option value="20">20 min</option>
                                    <option value="30" selected>30 min</option>
                                    <option value="45">45 min</option>
                                    <option value="60">60 min</option>
                                </select>
                            </div>
                            <div>
                                <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition ease-in-out duration-150">
                                    Agregar
                                </button>
                            </div>
                        </form>
                    </div>
                @endif

                <div class="mt-6">
                    <a href="{{ route('medicos.index') }}" class="text-indigo-600 hover:text-indigo-900 font-medium">&larr; Volver a médicos</a>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>