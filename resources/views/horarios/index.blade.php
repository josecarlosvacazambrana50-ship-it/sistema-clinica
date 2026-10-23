<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Horarios de atención — {{ $medico->nombre_completo }}</h2>
            <a href="{{ route('medicos.index') }}" class="text-sm text-indigo-600 hover:underline">← Médicos</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow-sm sm:rounded-lg p-4 sm:p-6 overflow-x-auto">
                <p class="text-sm text-gray-600 mb-3">Especialidades: {{ $medico->especialidades->pluck('nombre')->join(', ') }}</p>
                <table class="w-full text-sm text-left">
                    <thead class="text-gray-600 border-b"><tr><th class="py-2">Día</th><th>Hora inicial</th><th>Hora final</th><th>Duración de cita</th><th>Estado</th><th></th></tr></thead>
                    <tbody>
                    @forelse ($horarios as $h)
                        <tr class="border-b">
                            <td class="py-2 font-medium">{{ $h->dia_nombre }}</td>
                            <td>{{ substr($h->hora_inicio, 0, 5) }}</td>
                            <td>{{ substr($h->hora_fin, 0, 5) }}</td>
                            <td>{{ $h->duracion_cita_min }} min</td>
                            <td><x-estado :valor="$h->estado" /></td>
                            <td class="text-right">
                                @can('permiso', ['horarios', 'modificar'])
                                    <form method="POST" action="{{ route('horarios.estado', [$medico, $h]) }}" class="inline">
                                        @csrf @method('PATCH')
                                        <button class="text-gray-600 hover:underline">{{ $h->estado === 'activo' ? 'Desactivar' : 'Activar' }}</button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-6 text-center text-gray-500">Este médico aún no tiene horarios.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            @can('permiso', ['horarios', 'crear'])
            <div class="bg-white shadow-sm sm:rounded-lg p-4 sm:p-6">
                <h3 class="font-semibold text-[#1F3864] mb-3">Agregar horario</h3>
                <form method="POST" action="{{ route('horarios.store', $medico) }}" data-validar class="grid grid-cols-1 sm:grid-cols-5 gap-3 items-end">
                    @csrf
                    <div>
                        <x-input-label for="dia_semana" value="Día *" />
                        <select id="dia_semana" name="dia_semana" required class="block mt-1 w-full rounded-md border-gray-300 text-sm">
                            @foreach (\App\Models\HorarioAtencion::DIAS as $n => $d)<option value="{{ $n }}" @selected(old('dia_semana') == $n)>{{ $d }}</option>@endforeach
                        </select>
                    </div>
                    <div><x-input-label for="hora_inicio" value="Hora inicial *" /><input id="hora_inicio" type="time" name="hora_inicio" required value="{{ old('hora_inicio') }}" class="block mt-1 w-full rounded-md border-gray-300 text-sm"></div>
                    <div><x-input-label for="hora_fin" value="Hora final *" /><input id="hora_fin" type="time" name="hora_fin" required value="{{ old('hora_fin') }}" class="block mt-1 w-full rounded-md border-gray-300 text-sm"></div>
                    <div>
                        <x-input-label for="duracion_cita_min" value="Duración (min) *" />
                        <select id="duracion_cita_min" name="duracion_cita_min" required class="block mt-1 w-full rounded-md border-gray-300 text-sm">
                            @foreach (\App\Models\HorarioAtencion::DURACIONES as $d)<option value="{{ $d }}" @selected(old('duracion_cita_min', 30) == $d)>{{ $d }}</option>@endforeach
                        </select>
                    </div>
                    <button class="px-4 py-2 bg-green-600 text-white rounded-md text-sm">Agregar</button>
                </form>
                <x-input-error :messages="$errors->get('hora_inicio')" class="mt-2" />
                <x-input-error :messages="$errors->get('hora_fin')" class="mt-2" />
                <x-input-error :messages="$errors->get('dia_semana')" class="mt-2" />
            </div>
            @endcan
        </div>
    </div>
</x-app-layout>
