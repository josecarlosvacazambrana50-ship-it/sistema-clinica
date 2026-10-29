<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Reprogramar cita {{ $cita->codigo }}</h2>
    </x-slot>

    <div class="py-8" id="reprogramar-cita"
        data-cita="{{ $cita->id }}"
        data-disponibilidad="{{ route('citas.disponibilidad', $cita->medico) }}">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <section class="rounded-lg bg-white p-4 shadow-sm sm:p-6">
                <h3 class="mb-4 font-semibold text-[#1F3864]">Datos actuales (solo lectura)</h3>
                <dl class="grid grid-cols-1 gap-3 text-sm sm:grid-cols-2">
                    <div><dt class="text-gray-500">Paciente</dt><dd class="font-medium">{{ $cita->paciente->nombre_completo }}</dd></div>
                    <div><dt class="text-gray-500">Médico</dt><dd class="font-medium">{{ $cita->medico->nombre_completo }}</dd></div>
                    <div><dt class="text-gray-500">Especialidad</dt><dd class="font-medium">{{ $cita->especialidad->nombre }}</dd></div>
                    <div><dt class="text-gray-500">Fecha y hora actual</dt><dd class="font-medium">{{ $cita->fecha->format('d/m/Y') }} · {{ $cita->hora_inicio_corta }}–{{ $cita->hora_fin_corta }}</dd></div>
                </dl>
            </section>

            <form method="POST" action="{{ route('citas.reprogramar', $cita) }}" data-validar class="rounded-lg bg-white p-4 shadow-sm sm:p-6">
                @csrf
                @method('PUT')
                <div>
                    <x-input-label for="fecha" value="Nueva fecha *" />
                    <input id="fecha" type="date" name="fecha" required min="{{ today()->toDateString() }}"
                        value="{{ old('fecha', $cita->fecha->toDateString()) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 text-sm">
                    <x-input-error :messages="$errors->get('fecha')" class="mt-1" />
                </div>

                <div class="mt-5">
                    <x-input-label value="Nueva hora *" />
                    <input type="hidden" name="hora_inicio" id="hora_inicio">
                    <p id="msg-disponibilidad" class="my-2 text-sm text-gray-500">Consultando disponibilidad...</p>
                    <div id="bloques" class="flex flex-wrap gap-2"></div>
                    <p class="mt-2 text-xs text-gray-500">Los horarios tachados están ocupados o ya pasaron.</p>
                    <x-input-error :messages="$errors->get('hora_inicio')" class="mt-1" />
                </div>

                <div class="mt-5">
                    <x-input-label for="observacion" value="Observación (opcional)" />
                    <textarea id="observacion" name="observacion" maxlength="200" rows="2"
                        class="mt-1 block w-full rounded-md border-gray-300 text-sm">{{ old('observacion') }}</textarea>
                    <x-input-error :messages="$errors->get('observacion')" class="mt-1" />
                </div>

                <div class="mt-6 flex gap-2">
                    <button id="btn-guardar" type="submit" disabled class="rounded-md bg-green-600 px-4 py-2 text-sm text-white disabled:opacity-40">Guardar cambios</button>
                    <a href="{{ route('citas.show', $cita) }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm text-gray-700">Volver</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
