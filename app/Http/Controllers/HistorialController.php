<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HistorialController extends Controller
{
    public function dePaciente(Request $request, Paciente $paciente): View
    {
        abort_if(
            $request->user()->rol->nombre === 'Paciente' && $request->user()->paciente_id !== $paciente->id,
            403
        );

        return $this->mostrar($request, $paciente);
    }

    public function propio(Request $request): View
    {
        return $this->mostrar($request, $request->user()->paciente);
    }

    private function mostrar(Request $request, ?Paciente $paciente): View
    {
        $filtros = $request->validate([
            'estado' => ['nullable', 'in:'.implode(',', array_keys(Cita::ESTADOS))],
            'fecha_desde' => ['nullable', 'date'],
            'fecha_hasta' => ['nullable', 'date', 'after_or_equal:fecha_desde'],
        ]);

        $citas = $paciente
            ? $paciente->citas()->with(['medico', 'especialidad', 'historial.usuario'])
                ->when($filtros['estado'] ?? null, fn ($q, $estado) => $q->where('estado', $estado))
                ->when($filtros['fecha_desde'] ?? null, fn ($q, $fecha) => $q->whereDate('fecha', '>=', $fecha))
                ->when($filtros['fecha_hasta'] ?? null, fn ($q, $fecha) => $q->whereDate('fecha', '<=', $fecha))
                ->orderByDesc('fecha')->orderByDesc('hora_inicio')
                ->paginate(12)->withQueryString()
            : Cita::whereRaw('1 = 0')->paginate(12);

        return view('pacientes.historial', [
            'paciente' => $paciente,
            'citas' => $citas,
            'filtros' => $request->only('estado', 'fecha_desde', 'fecha_hasta'),
        ]);
    }
}
