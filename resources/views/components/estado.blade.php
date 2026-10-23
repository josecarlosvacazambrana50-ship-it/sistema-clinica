@props(['valor'])
@php
    $clase = match ($valor) {
        'activo', 'confirmada', 'atendida' => 'bg-green-100 text-green-800',
        'programada' => 'bg-blue-100 text-blue-800',
        'cancelada', 'inasistencia' => 'bg-red-100 text-red-800',
        default => 'bg-gray-200 text-gray-700',
    };
@endphp
<span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $clase }}">{{ ucfirst($valor) }}</span>
