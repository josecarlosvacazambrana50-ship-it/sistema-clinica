<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Medico;
use App\Services\AgendaService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AgendaController extends Controller
{
    public function __construct(private AgendaService $agenda)
    {
    }

    public function index(Request $request): View
    {
        $filtros = $request->validate([
            'fecha' => ['nullable', 'date'],
            'estado' => ['nullable', 'in:' . implode(',', array_keys(Cita::ESTADOS))],
        ]);
        $usuario = $request->user();
        if (! $usuario->esMedico()) {
            $filtros['medico_id'] = $request->validate([
                'medico_id' => ['nullable', 'integer', 'exists:medicos,id'],
            ])['medico_id'] ?? null;
        }
        $medicoId = $usuario->esMedico()
            ? $usuario->medico_id
            : ($filtros['medico_id'] ?? Medico::query()->orderBy('apellidos')->value('id'));
        $fecha = $filtros['fecha'] ?? today()->toDateString();

        $todasLasCitas = $medicoId ? $this->agenda->agendaDelDia($medicoId, $fecha) : collect();
        $citas = isset($filtros['estado'])
            ? $todasLasCitas->where('estado', $filtros['estado'])->values()
            : $todasLasCitas;
        $medico = $medicoId ? Medico::findOrFail($medicoId) : null;
        $bloques = $medico ? $this->agenda->bloques($medico, Carbon::parse($fecha)) : [];
        $totales = collect(array_fill_keys(array_keys(Cita::ESTADOS), 0))
            ->merge($todasLasCitas->countBy('estado'));

        return view('agenda.index', [
            'citas' => $citas,
            'medicos' => $usuario->esMedico() ? collect([$medico]) : Medico::orderBy('apellidos')->get(),
            'medico' => $medico,
            'bloques' => $bloques,
            'totales' => $totales,
            'filtros' => ['medico_id' => $medicoId, 'fecha' => $fecha, 'estado' => $filtros['estado'] ?? ''],
            'mostrarMotivo' => $usuario->tienePermiso('citas', 'mostrar'),
        ]);
    }
}
