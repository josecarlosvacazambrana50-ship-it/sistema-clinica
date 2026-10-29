<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Cita {{ $cita->codigo }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow-sm sm:rounded-lg p-4 sm:p-6">
                @if (in_array($cita->estado, ['programada', 'confirmada'], true) && Auth::user()->tienePermiso('citas', 'reprogramar'))
                    <a href="{{ route('citas.reprogramar.form', $cita) }}" class="inline-flex rounded-md bg-[#2F5597] px-4 py-2 text-sm text-white hover:bg-[#1F3864]">
                        Reprogramar cita
                    </a>
                @endif
                @if (in_array($cita->estado, ['programada', 'confirmada'], true) && Auth::user()->tienePermiso('citas', 'cancelar'))
                    <div x-data="{ open: @js($errors->has('motivo_cancelacion')) }" class="inline-block">
                        <button type="button" @click="open = true" class="ml-2 inline-flex rounded-md bg-red-600 px-4 py-2 text-sm text-white hover:bg-red-700">
                            Cancelar cita
                        </button>
                        <div x-show="open" x-cloak @keydown.escape.window="open = false"
                            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" role="dialog" aria-modal="true" aria-labelledby="modal-cancelar-titulo">
                            <section @click.outside="open = false" class="w-full max-w-lg rounded-lg bg-white p-6 shadow-xl">
                                <h3 id="modal-cancelar-titulo" class="text-lg font-semibold text-gray-900">Cancelar cita {{ $cita->codigo }}</h3>
                                <p class="mt-2 text-sm text-gray-600">La cita permanecerá en el historial y el horario quedará disponible.</p>
                                <form method="POST" action="{{ route('citas.cancelar', $cita) }}" data-validar class="mt-4">
                                    @csrf
                                    <label for="motivo_cancelacion" class="block text-sm font-medium text-gray-700">Motivo de cancelación *</label>
                                    <textarea id="motivo_cancelacion" name="motivo_cancelacion" required maxlength="200" rows="3"
                                        class="mt-1 block w-full rounded-md border-gray-300 text-sm">{{ old('motivo_cancelacion') }}</textarea>
                                    <x-input-error :messages="$errors->get('motivo_cancelacion')" class="mt-1" />
                                    <div class="mt-4 flex justify-end gap-2">
                                        <button type="button" @click="open = false" class="rounded-md border border-gray-300 px-4 py-2 text-sm text-gray-700">Volver</button>
                                        <button type="submit" class="rounded-md bg-red-600 px-4 py-2 text-sm text-white">Confirmar cancelación</button>
                                    </div>
                                </form>
                            </section>
                        </div>
                    </div>
                @endif
                @foreach (array_diff(\App\Models\Cita::TRANSICIONES[$cita->estado], ['cancelada']) as $estadoDestino)
                    @php
                        $permisoEstado = $estadoDestino === 'confirmada' ? 'confirmar' : 'atender';
                        $puedeGestionarEstado = Auth::user()->tienePermiso('citas', $permisoEstado)
                            && (!Auth::user()->esMedico() || Auth::user()->medico_id === $cita->medico_id)
                            && (!in_array($estadoDestino, ['atendida', 'inasistencia'], true) || !$cita->fecha->isAfter(today()));
                    @endphp
                    @if ($puedeGestionarEstado)
                        <form method="POST" action="{{ route('citas.estado', $cita) }}" class="mr-2 inline-block">
                            @csrf
                            <input type="hidden" name="estado" value="{{ $estadoDestino }}">
                            <button type="submit" class="inline-flex rounded-md bg-indigo-600 px-4 py-2 text-sm text-white hover:bg-indigo-700">
                                {{ ['confirmada' => 'Confirmar cita', 'atendida' => 'Marcar atendida', 'inasistencia' => 'Registrar inasistencia'][$estadoDestino] }}
                            </button>
                        </form>
                    @endif
                @endforeach
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
