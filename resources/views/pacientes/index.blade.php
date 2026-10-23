<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Gestión de pacientes</h2>
            @can('permiso', ['pacientes', 'crear'])
                <a href="{{ route('pacientes.create') }}" class="px-4 py-2 bg-[#2F5597] text-white rounded-md text-sm hover:bg-[#1F3864]">+ Registrar paciente</a>
            @endcan        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-4 sm:p-6">
                <form method="GET" class="flex gap-2 mb-4">
                    <input name="q" value="{{ $buscar }}" placeholder="Buscar por CI, nombre o apellido..." class="w-full sm:w-96 rounded-md border-gray-300 text-sm">
                    <button class="px-4 py-2 bg-[#2F5597] text-white rounded-md text-sm">Buscar</button>
                </form>
                <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-gray-600 border-b"><tr><th class="py-2">Código</th><th>CI</th><th>Paciente</th><th class="hidden md:table-cell">Teléfono</th><th>Estado</th><th></th></tr></thead>
                    <tbody>
                    @forelse ($pacientes as $p)
                        <tr class="border-b">
                            <td class="py-2 font-medium">{{ $p->codigo }}</td>
                            <td>{{ $p->ci }}</td>
                            <td>{{ $p->nombre_completo }}</td>
                            <td class="hidden md:table-cell">{{ $p->telefono }}</td>
                            <td><x-estado :valor="$p->estado" /></td>
                            <td class="text-right whitespace-nowrap">
                                <a href="{{ route('pacientes.show', $p) }}" class="text-indigo-600 hover:underline">Ver / historial</a>
                                @can('permiso', ['pacientes', 'modificar'])
                                    <a href="{{ route('pacientes.edit', $p) }}" class="ms-3 text-indigo-600 hover:underline">Editar</a>
                                    <form method="POST" action="{{ route('pacientes.estado', $p) }}" class="inline" onsubmit="return confirm('¿Cambiar el estado del paciente?')">
                                        @csrf @method('PATCH')
                                        <button class="ms-3 text-gray-600 hover:underline">{{ $p->estado === 'activo' ? 'Desactivar' : 'Activar' }}</button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-6 text-center text-gray-500">No se encontraron pacientes.</td></tr>
                    @endforelse
                    </tbody>
                </table>
                </div>
                <div class="mt-4">{{ $pacientes->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
