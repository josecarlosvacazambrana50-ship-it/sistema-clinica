<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Citas médicas</h2>
            @can('permiso', ['citas', 'crear'])
                <a href="{{ route('citas.create') }}" class="px-4 py-2 bg-[#2F5597] text-white rounded-md text-sm hover:bg-[#1F3864]">+ Nueva cita</a>
            @endcan
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-4 sm:p-6">
                <form method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3 mb-4">
                    <input type="date" name="fecha" value="{{ $filtros['fecha'] ?? '' }}" class="rounded-md border-gray-300 text-sm">
                    @unless (Auth::user()->esMedico() || Auth::user()->esPaciente())
                        <select name="medico_id" class="rounded-md border-gray-300 text-sm">
                            <option value="">Todos los médicos</option>
                            @foreach ($medicos as $m)
                                <option value="{{ $m->id }}" @selected(($filtros['medico_id'] ?? '') == $m->id)>{{ $m->nombre_completo }}</option>
                            @endforeach
                        </select>
                    @endunless
                    <select name="estado" class="rounded-md border-gray-300 text-sm">
                        <option value="">Todos los estados</option>
                        @foreach (\App\Models\Cita::ESTADOS as $k => $v)
                            <option value="{{ $k }}" @selected(($filtros['estado'] ?? '') === $k)>{{ $v }}</option>
                        @endforeach
                    </select>
                    <div class="flex gap-2">
                        <button class="px-4 py-2 bg-[#2F5597] text-white rounded-md text-sm">Filtrar</button>
                        <a href="{{ route('citas.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm text-gray-700">Limpiar</a>
                    </div>
                </form>
                @include('citas._tabla', ['citas' => $citas])
                <div class="mt-4">{{ $citas->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
