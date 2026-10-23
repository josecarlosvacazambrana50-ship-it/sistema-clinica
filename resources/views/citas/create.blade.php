<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Programar cita médica</h2>
    </x-slot>

    <div class="py-8" id="programar-cita" data-base="{{ url('/citas') }}">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('citas.store') }}" data-validar class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                @csrf
                <div class="bg-white shadow-sm sm:rounded-lg p-4 sm:p-6 space-y-4">
                    <h3 class="font-semibold text-[#1F3864]">Datos de la cita</h3>

                    <div>
                        <x-input-label for="paciente_id" value="Paciente *" />
                        <select id="paciente_id" name="paciente_id" required class="block mt-1 w-full rounded-md border-gray-300 text-sm">
                            <option value="">Seleccione...</option>
                            @foreach ($pacientes as $p)
                                <option value="{{ $p->id }}" @selected(old('paciente_id', request('paciente_id')) == $p->id)>{{ $p->codigo }} · {{ $p->nombre_completo }} (CI {{ $p->ci }})</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('paciente_id')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="especialidad_id" value="Especialidad *" />
                        <select id="especialidad_id" name="especialidad_id" required class="block mt-1 w-full rounded-md border-gray-300 text-sm">
                            <option value="">Seleccione...</option>
                            @foreach ($especialidades as $e)
                                <option value="{{ $e->id }}">{{ $e->nombre }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('especialidad_id')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="medico_id" value="Médico *" />
                        <select id="medico_id" name="medico_id" required class="block mt-1 w-full rounded-md border-gray-300 text-sm">
                            <option value="">Seleccione una especialidad primero</option>
                        </select>
                        <x-input-error :messages="$errors->get('medico_id')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="fecha" value="Fecha *" />
                        <input id="fecha" type="date" name="fecha" required min="{{ today()->toDateString() }}" class="block mt-1 w-full rounded-md border-gray-300 text-sm">
                        <x-input-error :messages="$errors->get('fecha')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="motivo" value="Motivo de la consulta (opcional)" />
                        <x-text-input id="motivo" name="motivo" type="text" maxlength="200" class="block mt-1 w-full" :value="old('motivo')" />
                    </div>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg p-4 sm:p-6">
                    <h3 class="font-semibold text-[#1F3864] mb-3">Horarios disponibles</h3>
                    <input type="hidden" name="hora_inicio" id="hora_inicio" value="{{ old('hora_inicio') }}">
                    <input type="hidden" name="hora_fin" id="hora_fin" value="{{ old('hora_fin') }}">

                    <p id="msg-seleccione" class="text-sm text-gray-500">Seleccione médico y fecha para ver las horas libres.</p>
                    <p id="msg-cargando" class="hidden text-sm text-gray-500">Consultando disponibilidad...</p>
                    <p id="msg-vacio" class="hidden text-sm text-amber-700">El médico no atiende en esa fecha.</p>

                    <div id="bloques" class="flex flex-wrap gap-2"></div>
                    <p id="msg-leyenda" class="hidden mt-3 text-xs text-gray-500">Los horarios tachados ya están ocupados o fuera de tiempo.</p>
                    <x-input-error :messages="$errors->get('hora_inicio')" class="mt-2" />

                    <div class="mt-6 flex gap-2">
                        <button type="submit" id="btn-guardar" disabled class="px-4 py-2 bg-green-600 text-white rounded-md text-sm disabled:opacity-40">Guardar cita</button>
                        <a href="{{ route('citas.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm text-gray-700">Cancelar</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
