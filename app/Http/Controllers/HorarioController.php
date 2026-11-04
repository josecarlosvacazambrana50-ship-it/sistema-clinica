<?php

namespace App\Http\Controllers;

use App\Models\Medico;
use App\Models\HorarioAtencion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HorarioController extends Controller
{
    private function verificarAdmin(): void
    {
        $usuario = auth()->user();
        $esAdmin = ($usuario->rol_id == 1 || optional($usuario->rol)->nombre === 'Administrador');
        
        if (! $esAdmin) {
            abort(403, 'No autorizado.');
        }
    }

    public function index(Medico $medico): View
    {
        $medico->load('horarios');

        return view('horarios.index', compact('medico'));
    }

    public function store(Request $request, Medico $medico): RedirectResponse
    {
        $this->verificarAdmin();

        $validated = $request->validate([
            'dia' => ['required'],
            'hora_inicial' => ['required', 'date_format:H:i'],
            'hora_final' => ['required', 'date_format:H:i', 'after:hora_inicial'],
            'duracion' => ['required', 'integer', 'min:5'],
        ]);

        $medico->horarios()->create([
            'dia_semana' => $validated['dia'],
            'hora_inicio' => $validated['hora_inicial'],
            'hora_fin' => $validated['hora_final'],
            'duracion_cita_min' => $validated['duracion'],
            'estado' => 'activo',
        ]);

        return redirect()->route('horarios.index', $medico)
            ->with('success', 'Horario de atención agregado correctamente.');
    }

    public function update(Request $request, Medico $medico, $horarioId): RedirectResponse
    {
        $this->verificarAdmin();

        $horario = $medico->horarios()->findOrFail($horarioId);

        $validated = $request->validate([
            'dia' => ['required'],
            'hora_inicial' => ['required', 'date_format:H:i'],
            'hora_final' => ['required', 'date_format:H:i', 'after:hora_inicial'],
            'duracion' => ['required', 'integer', 'min:5'],
        ]);

        $horario->update([
            'dia_semana' => $validated['dia'],
            'hora_inicio' => $validated['hora_inicial'],
            'hora_fin' => $validated['hora_final'],
            'duracion_cita_min' => $validated['duracion'],
        ]);

        return redirect()->route('horarios.index', $medico)
            ->with('success', 'Horario actualizado correctamente.');
    }

    public function cambiarEstado(Medico $medico, $horarioId): RedirectResponse
    {
        $this->verificarAdmin();

        $horario = $medico->horarios()->findOrFail($horarioId);

        $nuevoEstado = ($horario->estado === 'activo') ? 'inactivo' : 'activo';

        $horario->update([
            'estado' => $nuevoEstado,
        ]);

        return redirect()->route('horarios.index', $medico)
            ->with('success', 'El estado del horario fue actualizado.');
    }

    public function destroy(Medico $medico, $horarioId): RedirectResponse
    {
        $this->verificarAdmin();

        $horario = $medico->horarios()->findOrFail($horarioId);
        $horario->delete();

        return redirect()->route('horarios.index', $medico)
            ->with('success', 'El horario de atención fue eliminado correctamente.');
    }
}