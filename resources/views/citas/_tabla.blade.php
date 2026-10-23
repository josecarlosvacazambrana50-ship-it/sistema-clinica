<div class="overflow-x-auto">
    <table class="w-full text-sm text-left">
        <thead class="text-gray-600 border-b">
            <tr>
                <th class="py-2">Código</th><th>Fecha</th><th>Hora</th><th>Paciente</th>
                <th class="hidden md:table-cell">Médico</th><th class="hidden md:table-cell">Especialidad</th><th>Estado</th><th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($citas as $c)
                <tr class="border-b">
                    <td class="py-2 font-medium">{{ $c->codigo }}</td>
                    <td>{{ $c->fecha->format('d/m/Y') }}</td>
                    <td>{{ $c->hora_inicio_corta }} – {{ $c->hora_fin_corta }}</td>
                    <td>{{ $c->paciente->nombre_completo }}</td>
                    <td class="hidden md:table-cell">Dr(a). {{ $c->medico->nombre_completo }}</td>
                    <td class="hidden md:table-cell">{{ $c->especialidad->nombre }}</td>
                    <td><x-estado :valor="$c->estado" /></td>
                    <td class="text-right"><a href="{{ route('citas.show', $c) }}" class="text-indigo-600 hover:underline">Ver</a></td>
                </tr>
            @empty
                <tr><td colspan="8" class="py-6 text-center text-gray-500">No hay citas para mostrar.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
