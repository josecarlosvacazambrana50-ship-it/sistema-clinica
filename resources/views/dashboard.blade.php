<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Panel de control</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @unless (Auth::user()->esPaciente())
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach ([['citas_hoy', 'Citas de hoy'], ['pacientes', 'Pacientes activos'], ['medicos', 'Médicos activos'], ['inasist', 'Inasistencias del mes']] as [$k, $t])
                        <div class="bg-white shadow-sm sm:rounded-lg p-5">
                            <div class="text-3xl font-bold text-[#1F3864]">{{ $resumen[$k] }}</div>
                            <div class="text-sm text-gray-500">{{ $t }}</div>
                        </div>
                    @endforeach
                </div>
            @endunless

            <div class="bg-white shadow-sm sm:rounded-lg p-4 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-[#1F3864]">Próximas citas</h3>
                    @can('permiso', ['citas', 'crear'])
                        <a href="{{ route('citas.create') }}" class="px-4 py-2 bg-[#2F5597] text-white rounded-md text-sm hover:bg-[#1F3864]">+ Nueva cita</a>
                    @endcan
                </div>
                @include('citas._tabla', ['citas' => $proximas])
            </div>
        </div>
    </div>
</x-app-layout>
