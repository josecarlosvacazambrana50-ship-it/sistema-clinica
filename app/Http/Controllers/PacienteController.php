<?php

namespace App\Http\Controllers;

use App\Http\Requests\PacienteRequest;
use App\Models\Paciente;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/** CU07 - Gestionar Pacientes */
class PacienteController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permiso:pacientes,mostrar', only: ['index', 'show']),
            new Middleware('permiso:pacientes,crear', only: ['create', 'store']),
            new Middleware('permiso:pacientes,modificar', only: ['edit', 'update', 'cambiarEstado']),
        ];
    }

    public function index(Request $request): View
    {
        $buscar = trim((string) $request->query('q'));

        $pacientes = Paciente::buscar($buscar)
            ->orderBy('apellidos')->orderBy('nombres')
            ->paginate(10)->withQueryString();

        return view('pacientes.index', compact('pacientes', 'buscar'));
    }

    public function create(): View
    {
        return view('pacientes.create');
    }

    public function store(PacienteRequest $request): RedirectResponse
    {
        $paciente = DB::transaction(function () use ($request) {
            $p = Paciente::create($request->validated());
            $p->update(['codigo' => 'PAC-' . str_pad((string) $p->id, 4, '0', STR_PAD_LEFT)]);

            return $p;
        });

        return redirect()->route('pacientes.show', $paciente)->with('success', 'Paciente registrado correctamente.');
    }

    public function show(Paciente $paciente): View
    {
        $citas = $paciente->citas()->with(['medico', 'especialidad'])
            ->orderByDesc('fecha')->orderByDesc('hora_inicio')->get();

        return view('pacientes.show', compact('paciente', 'citas'));
    }

    public function edit(Paciente $paciente): View
    {
        return view('pacientes.edit', compact('paciente'));
    }

    public function update(PacienteRequest $request, Paciente $paciente): RedirectResponse
    {
        $paciente->update($request->validated());

        return redirect()->route('pacientes.show', $paciente)->with('success', 'Datos del paciente actualizados.');
    }

    /** Se desactiva sin eliminar el historial de citas (RN11). */
    public function cambiarEstado(Paciente $paciente): RedirectResponse
    {
        $paciente->update(['estado' => $paciente->estado === 'activo' ? 'inactivo' : 'activo']);

        return back()->with('success', 'Estado del paciente actualizado.');
    }
}
