<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Médicos y horarios</h2>
            @can('permiso', ['medicos', 'crear'])
                <a href="{{ route('medicos.create') }}" class="px-4 py-2 bg-[#2F5597] text-white rounded-md text-sm hover:bg-[#1F3864]">+ Nuevo médico</a>
            @endcan        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-4 sm:p-6">
                <form method="GET" class="flex gap-2 mb-4">
                    <input name="q" value="{{ $buscar }}" placeholder="Buscar por nombre o especialidad..." class="w-full sm:w-80 rounded-md border-gray-300 text-sm">
                    <button class="px-4 py-2 bg-[#2F5597] text-white rounded-md text-sm">Buscar</button>
                </form>
                <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-gray-600 border-b"><tr><th class="py-2">Código</th><th>Médico</th><th>Registro</th><th class="hidden md:table-cell">Especialidades</th><th>Estado</th><th></th></tr></thead>
                    <tbody>
                    @forelse ($medicos as $m)
                        <tr class="border-b">
                            <td class="py-2 font-medium">{{ $m->codigo }}</td>
                            <td>{{ $m->nombre_completo }}</td>
                            <td>{{ $m->nro_registro }}</td>
                            <td class="hidden md:table-cell">{{ $m->especialidades->pluck('nombre')->join(', ') }}</td>
                            <td><x-estado :valor="$m->estado" /></td>
                            <td class="text-right whitespace-nowrap">
                                @can('permiso', ['horarios', 'mostrar'])<a href="{{ route('horarios.index', $m) }}" class="text-indigo-600 hover:underline">Horarios</a>@endcan
                                @can('permiso', ['medicos', 'modificar'])
                                    <a href="{{ route('medicos.edit', $m) }}" class="ms-3 text-indigo-600 hover:underline">Editar</a>
                                    <form method="POST" action="{{ route('medicos.estado', $m) }}" class="inline" onsubmit="return confirm('¿Cambiar el estado del médico?')">
                                        @csrf @method('PATCH')
                                        <button class="ms-3 text-gray-600 hover:underline">{{ $m->estado === 'activo' ? 'Desactivar' : 'Activar' }}</button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-6 text-center text-gray-500">No hay médicos registrados.</td></tr>
                    @endforelse
                    </tbody>
                </table>
                </div>
                <div class="mt-4">{{ $medicos->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
