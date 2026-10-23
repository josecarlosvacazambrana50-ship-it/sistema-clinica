<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Usuarios</h2>
            @can('permiso', ['usuarios', 'crear'])
                <a href="{{ route('usuarios.create') }}" class="px-4 py-2 bg-[#2F5597] text-white rounded-md text-sm hover:bg-[#1F3864]">+ Nuevo usuario</a>
            @endcan        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-4 sm:p-6">
                <form method="GET" class="flex gap-2 mb-4">
                    <input name="q" value="{{ $buscar }}" placeholder="Buscar por correo..." class="w-full sm:w-80 rounded-md border-gray-300 text-sm">
                    <button class="px-4 py-2 bg-[#2F5597] text-white rounded-md text-sm">Buscar</button>
                </form>
                <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-gray-600 border-b"><tr><th class="py-2">Correo</th><th>Rol</th><th>Estado</th><th class="hidden md:table-cell">Último acceso</th><th></th></tr></thead>
                    <tbody>
                    @foreach ($usuarios as $u)
                        <tr class="border-b">
                            <td class="py-2 font-medium">{{ $u->email }}</td>
                            <td>{{ $u->rol->nombre }}</td>
                            <td><x-estado :valor="$u->estado" /></td>
                            <td class="hidden md:table-cell">{{ $u->ultimo_acceso?->format('d/m/Y H:i') ?? '—' }}</td>
                            <td class="text-right whitespace-nowrap">
                                @can('permiso', ['usuarios', 'modificar'])
                                    <a href="{{ route('usuarios.edit', $u) }}" class="text-indigo-600 hover:underline">Editar</a>
                                    @if ($u->id !== Auth::id())
                                    <form method="POST" action="{{ route('usuarios.estado', $u) }}" class="inline" onsubmit="return confirm('¿Cambiar el estado del usuario?')">
                                        @csrf @method('PATCH')
                                        <button class="ms-3 text-gray-600 hover:underline">{{ $u->estado === 'activo' ? 'Desactivar' : 'Activar' }}</button>
                                    </form>
                                    @endif
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                </div>
                <div class="mt-4">{{ $usuarios->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
