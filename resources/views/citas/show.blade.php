<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detalle de la Cita Médica') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900">
                
                <!-- Mensajes de Error o Éxito -->
                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('success'))
                    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Información General de la Cita -->
                <div class="mb-6 border-b pb-4">
                    <div class="flex justify-between items-center mb-2">
                        <h3 class="text-lg font-medium text-gray-900">Información de la Consulta</h3>
                        
                        {{-- BOTÓN DE CANCELAR CITA --}}
                        @if(in_array($cita->estado, ['programada', 'confirmada']))
                            <form action="{{ route('citas.cancelar', $cita->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas cancelar esta cita médica?');">
                                @csrf
                                <input type="hidden" name="motivo_cancelacion" value="Cancelada desde el detalle de la cita">
                                <button type="submit" class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded transition">
                                    Cancelar Cita
                                </button>
                            </form>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <p><strong>Código:</strong> {{ $cita->codigo ?? 'N/D' }}</p>
                        <p><strong>Fecha:</strong> {{ $cita->fecha ? $cita->fecha->format('d/m/Y') : 'N/D' }}</p>
                        <p><strong>Hora:</strong> {{ isset($cita->hora_inicio) ? substr($cita->hora_inicio, 0, 5) : 'N/D' }}</p>
                        <p><strong>Paciente:</strong> {{ trim(($cita->paciente->nombres ?? '') . ' ' . ($cita->paciente->apellidos ?? '')) ?: 'N/D' }}</p>
                        <p><strong>Médico:</strong> {{ $cita->medico->nombre_completo ?? trim(($cita->medico->nombres ?? '') . ' ' . ($cita->medico->apellidos ?? '')) ?: 'N/D' }}</p>
                        <p><strong>Estado:</strong> <span class="px-2 py-1 text-xs rounded bg-gray-200 uppercase font-semibold">{{ $cita->estado ?? 'Pendiente' }}</span></p>
                    </div>
                </div>

                @php
                    $user = auth()->user();
                    $esPaciente = ($user->rol_id == 3 || optional($user->rol)->nombre === 'Paciente');
                    $esAdmin = ($user->rol_id == 1 || optional($user->rol)->nombre === 'Administrador' || $user->can('permiso', ['citas', 'atender']));
                    $esAtendida = in_array($cita->estado, ['atendida', 'finalizada']);
                    $esDiaDeCita = $cita->fecha ? ($cita->fecha->isToday() || $cita->fecha->isPast()) : false;
                @endphp

                {{-- SI LA CITA YA FUE ATENDIDA --}}
                @if($esAtendida)
                    <div class="p-6 bg-gray-50 shadow-sm rounded-lg mt-6 border">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Atención Médica y Diagnóstico (Registrado)</h3>
                        
                        <div class="mb-4">
                            <label class="block font-medium text-sm text-gray-700">Diagnóstico</label>
                            <p class="mt-1 p-3 bg-white rounded-md border border-gray-200 text-gray-800">{{ $cita->diagnostico ?? 'Sin diagnóstico registrado.' }}</p>
                        </div>

                        <div class="mb-4">
                            <label class="block font-medium text-sm text-gray-700">Receta y Medicamentos</label>
                            <p class="mt-1 p-3 bg-white rounded-md border border-gray-200 text-gray-800">{{ $cita->receta ?? 'Sin receta registrada.' }}</p>
                        </div>

                        <div class="mb-4">
                            <label class="block font-medium text-sm text-gray-700">Notas de Evolución y Observaciones</label>
                            <p class="mt-1 p-3 bg-white rounded-md border border-gray-200 text-gray-800">{{ $cita->observaciones ?? 'Sin observaciones.' }}</p>
                        </div>
                    </div>

                {{-- SI LA CITA NO HA SIDO ATENDIDA AÚN --}}
                @else
                    @if($esPaciente)
                        <div class="p-6 bg-blue-50 border-l-4 border-blue-400 rounded-lg mt-6">
                            <p class="text-sm text-blue-700 font-medium">
                                Tu cita se encuentra en estado <strong>{{ $cita->estado }}</strong>. Los detalles del diagnóstico y la receta médica aparecerán aquí una vez que el médico te atienda en el consultorio.
                            </p>
                        </div>
                    @else
                        @if($esAdmin || $esDiaDeCita)
                            <div class="p-6 bg-gray-50 shadow-sm rounded-lg mt-6 border">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Atención Médica y Diagnóstico</h3>

                                <form action="{{ route('citas.atender', $cita->id) }}" method="POST">
                                    @csrf

                                    <!-- Diagnóstico -->
                                    <div class="mb-4">
                                        <label for="diagnostico" class="block font-medium text-sm text-gray-700">Diagnóstico</label>
                                        <textarea name="diagnostico" id="diagnostico" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>{{ old('diagnostico', $cita->diagnostico) }}</textarea>
                                    </div>

                                    <!-- Receta Médica -->
                                    <div class="mb-4">
                                        <label for="receta" class="block font-medium text-sm text-gray-700">Receta y Medicamentos</label>
                                        <textarea name="receta" id="receta" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>{{ old('receta', $cita->receta) }}</textarea>
                                    </div>

                                    <!-- Notas de Evolución / Observaciones -->
                                    <div class="mb-4">
                                        <label for="observaciones" class="block font-medium text-sm text-gray-700">Notas de Evolución y Observaciones</label>
                                        <textarea name="observaciones" id="observaciones" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('observaciones', $cita->observaciones) }}</textarea>
                                    </div>

                                    <div class="flex justify-end">
                                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition ease-in-out duration-150">
                                            Finalizar y Guardar Consulta
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @else
                            <div class="p-6 bg-amber-50 border-l-4 border-amber-400 rounded-lg mt-6">
                                <p class="text-sm text-amber-800 font-medium">
                                    Esta cita está programada para el <strong>{{ $cita->fecha->format('d/m/Y') }}</strong>. El formulario de atención médica se habilitará el día de la consulta.
                                </p>
                            </div>
                        @endif
                    @endif
                @endif

                <!-- Botón de Retorno -->
                <div class="mt-6">
                    <a href="{{ route('citas.index') }}" class="text-indigo-600 hover:text-indigo-900 font-medium">&larr; Volver al listado de citas</a>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>