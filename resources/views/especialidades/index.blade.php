<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Especialidades</h2>
            @can('permiso', ['especialidades', 'crear'])
                <a href="{{ route('especialidades.create') }}" class="px-4 py-2 bg-[#2F5597] text-white rounded-md text-sm hover:bg-[#1F3864]">+ Nueva especialidad</a>
            @endcan        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-4 sm:p-6 overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-gray-600 border-b"><tr><th class="py-2">Nombre</th><th class="hidden md:table-cell">Descripción</th><th>Médicos</th><th>Estado</th><th></th></tr></thead>
                    <tbody>
                    @foreach ($especialidades as $e)
                        <tr class="border-b">
                            <td class="py-2 font-medium">{{ $e->nombre }}</td>
                            <td class="hidden md:table-cell">{{ $e->descripcion }}</td>
                            <td>{{ $e->medicos_count }}</td>
                            <td><x-estado :valor="$e->estado" /></td>
                            <td class="text-right whitespace-nowrap">
                                @can('permiso', ['especialidades', 'modificar'])
                                    <a href="{{ route('especialidades.edit', $e) }}" class="text-indigo-600 hover:underline">Editar</a>
                                    <form method="POST" action="{{ route('especialidades.estado', $e) }}" class="inline" onsubmit="return confirm('¿Cambiar el estado de la especialidad?')">
                                        @csrf @method('PATCH')
                                        <button class="ms-3 text-gray-600 hover:underline">{{ $e->estado === 'activo' ? 'Desactivar' : 'Activar' }}</button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
