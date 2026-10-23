<?php

namespace App\Http\Controllers;

use App\Http\Requests\EspecialidadRequest;
use App\Models\Especialidad;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

/** CU04 - Gestionar Especialidades */
class EspecialidadController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permiso:especialidades,mostrar', only: ['index']),
            new Middleware('permiso:especialidades,crear', only: ['create', 'store']),
            new Middleware('permiso:especialidades,modificar', only: ['edit', 'update', 'cambiarEstado']),
        ];
    }

    public function index(): View
    {
        $especialidades = Especialidad::withCount('medicos')->orderBy('nombre')->get();

        return view('especialidades.index', compact('especialidades'));
    }

    public function create(): View
    {
        return view('especialidades.create');
    }

    public function store(EspecialidadRequest $request): RedirectResponse
    {
        Especialidad::create($request->safe()->only(['nombre', 'nombre_normalizado', 'descripcion']));

        return redirect()->route('especialidades.index')->with('success', 'Especialidad registrada.');
    }

    public function edit(Especialidad $especialidad): View
    {
        return view('especialidades.edit', compact('especialidad'));
    }

    public function update(EspecialidadRequest $request, Especialidad $especialidad): RedirectResponse
    {
        $especialidad->update($request->safe()->only(['nombre', 'nombre_normalizado', 'descripcion']));

        return redirect()->route('especialidades.index')->with('success', 'Especialidad actualizada.');
    }

    /** Activa o desactiva (no se elimina para conservar el historial). */
    public function cambiarEstado(Especialidad $especialidad): RedirectResponse
    {
        if ($especialidad->estado === 'activo' && $especialidad->medicos()->where('estado', 'activo')->exists()) {
            return back()->with('error', 'No se puede desactivar: hay médicos activos con esta especialidad.');
        }

        $especialidad->update(['estado' => $especialidad->estado === 'activo' ? 'inactivo' : 'activo']);

        return back()->with('success', 'Estado de la especialidad actualizado.');
    }
}
