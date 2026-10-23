<?php

namespace App\Http\Controllers;

use App\Http\Requests\MedicoRequest;
use App\Models\Especialidad;
use App\Models\Medico;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/** CU05 - Gestionar Médicos */
class MedicoController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permiso:medicos,mostrar', only: ['index', 'show']),
            new Middleware('permiso:medicos,crear', only: ['create', 'store']),
            new Middleware('permiso:medicos,modificar', only: ['edit', 'update', 'cambiarEstado']),
        ];
    }

    public function index(Request $request): View
    {
        $buscar = trim((string) $request->query('q'));

        $medicos = Medico::with('especialidades')
            ->when($buscar, function ($q) use ($buscar) {
                $like = '%' . mb_strtolower($buscar) . '%';
                $q->where(fn ($w) => $w->whereRaw('lower(nombres) like ?', [$like])
                    ->orWhereRaw('lower(apellidos) like ?', [$like])
                    ->orWhereHas('especialidades', fn ($e) => $e->whereRaw('lower(nombre) like ?', [$like])));
            })
            ->orderBy('apellidos')->orderBy('nombres')
            ->paginate(10)->withQueryString();

        return view('medicos.index', compact('medicos', 'buscar'));
    }

    public function create(): View
    {
        return view('medicos.create', ['especialidades' => Especialidad::activas()->orderBy('nombre')->get()]);
    }

    public function store(MedicoRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $medico = Medico::create($request->safe()->except('especialidades'));
            $medico->update(['codigo' => 'MED-' . str_pad((string) $medico->id, 4, '0', STR_PAD_LEFT)]);
            $medico->especialidades()->sync($request->validated('especialidades'));
        });

        return redirect()->route('medicos.index')->with('success', 'Médico registrado. Ahora defina sus horarios de atención.');
    }

    public function show(Medico $medico): View
    {
        $medico->load('especialidades', 'horarios');

        return view('medicos.show', compact('medico'));
    }

    public function edit(Medico $medico): View
    {
        $medico->load('especialidades');

        return view('medicos.edit', [
            'medico'         => $medico,
            'especialidades' => Especialidad::activas()->orWhereIn('id', $medico->especialidades->pluck('id'))->orderBy('nombre')->get(),
        ]);
    }

    public function update(MedicoRequest $request, Medico $medico): RedirectResponse
    {
        DB::transaction(function () use ($request, $medico) {
            $medico->update($request->safe()->except('especialidades'));
            $medico->especialidades()->sync($request->validated('especialidades'));
        });

        return redirect()->route('medicos.index')->with('success', 'Datos del médico actualizados.');
    }

    /** Un médico inactivo no recibe nuevas citas, pero conserva su historial (RN04, RN11). */
    public function cambiarEstado(Medico $medico): RedirectResponse
    {
        $medico->update(['estado' => $medico->estado === 'activo' ? 'inactivo' : 'activo']);

        return back()->with('success', 'Estado del médico actualizado.');
    }
}
