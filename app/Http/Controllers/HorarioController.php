<?php

namespace App\Http\Controllers;

use App\Http\Requests\HorarioRequest;
use App\Models\HorarioAtencion;
use App\Models\Medico;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/** CU06 - Gestionar Horarios de Atención */
class HorarioController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permiso:horarios,mostrar', only: ['index']),
            new Middleware('permiso:horarios,crear', only: ['store']),
            new Middleware('permiso:horarios,modificar', only: ['update', 'cambiarEstado']),
        ];
    }

    public function index(Medico $medico): View
    {
        return view('horarios.index', [
            'medico'   => $medico->load('especialidades'),
            'horarios' => $medico->horarios,
        ]);
    }

    public function store(HorarioRequest $request, Medico $medico): RedirectResponse
    {
        $this->sinSuperposicion($medico, $request->validated());
        $medico->horarios()->create($request->validated());

        return back()->with('success', 'Horario agregado.');
    }

    public function update(HorarioRequest $request, Medico $medico, HorarioAtencion $horario): RedirectResponse
    {
        abort_unless($horario->medico_id === $medico->id, 404);
        $this->sinSuperposicion($medico, $request->validated(), $horario->id);
        $horario->update($request->validated());

        return back()->with('success', 'Horario actualizado.');
    }

    public function cambiarEstado(Medico $medico, HorarioAtencion $horario): RedirectResponse
    {
        abort_unless($horario->medico_id === $medico->id, 404);
        $horario->update(['estado' => $horario->estado === 'activo' ? 'inactivo' : 'activo']);

        return back()->with('success', 'Estado del horario actualizado.');
    }

    /** Dos horarios del mismo médico y día no pueden superponerse. */
    private function sinSuperposicion(Medico $medico, array $d, ?int $ignorar = null): void
    {
        $choca = $medico->horarios()->activos()
            ->where('dia_semana', $d['dia_semana'])
            ->when($ignorar, fn ($q) => $q->where('id', '!=', $ignorar))
            ->where('hora_inicio', '<', $d['hora_fin'])
            ->where('hora_fin', '>', $d['hora_inicio'])
            ->exists();

        if ($choca) {
            throw ValidationException::withMessages([
                'hora_inicio' => 'El horario se superpone con otro horario del mismo día.',
            ]);
        }
    }
}
