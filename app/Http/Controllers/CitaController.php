<?php

namespace App\Http\Controllers;

use App\Http\Requests\CitaRequest;
use App\Models\Cita;
use App\Models\Especialidad;
use App\Models\Medico;
use App\Models\Paciente;
use App\Services\AgendaService;
use App\Services\CitaService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

/** CU08 - Programar Cita */
class CitaController extends Controller implements HasMiddleware
{
    public function __construct(private CitaService $citas, private AgendaService $agenda)
    {
    }

    public static function middleware(): array
    {
        return [
            new Middleware('permiso:citas,mostrar', only: ['index', 'show']),
            new Middleware('permiso:citas,crear', only: ['create', 'store', 'disponibilidad', 'medicosPorEspecialidad']),
        ];
    }

    public function index(Request $request): View
    {
        $user = $request->user();

        $citas = Cita::with(['paciente', 'medico', 'especialidad'])
            // Cada médico ve solo su agenda y cada paciente solo sus citas (RN12)
            ->when($user->esMedico(), fn ($q) => $q->where('medico_id', $user->medico_id))
            ->when($user->esPaciente(), fn ($q) => $q->where('paciente_id', $user->paciente_id))
            ->when($request->filled('fecha'), fn ($q) => $q->whereDate('fecha', $request->query('fecha')))
            ->when($request->filled('medico_id') && ! $user->esMedico(), fn ($q) => $q->where('medico_id', $request->query('medico_id')))
            ->when($request->filled('estado'), fn ($q) => $q->where('estado', $request->query('estado')))
            ->orderBy('fecha')->orderBy('hora_inicio')
            ->paginate(12)->withQueryString();

        return view('citas.index', [
            'citas'   => $citas,
            'medicos' => Medico::orderBy('apellidos')->get(),
            'filtros' => $request->only('fecha', 'medico_id', 'estado'),
        ]);
    }

    public function create(): View
    {
        return view('citas.create', [
            'pacientes'      => Paciente::activos()->orderBy('apellidos')->orderBy('nombres')->get(),
            'especialidades' => Especialidad::activas()->orderBy('nombre')->get(),
        ]);
    }

    public function store(CitaRequest $request): RedirectResponse
    {
        $cita = $this->citas->programar($request->validated(), $request->user());

        return redirect()->route('citas.show', $cita)
            ->with('success', "Cita {$cita->codigo} registrada para el {$cita->fecha->format('d/m/Y')} de {$cita->hora_inicio_corta} a {$cita->hora_fin_corta}.");
    }

    public function show(Request $request, Cita $cita): View
    {
        $user = $request->user();
        abort_if($user->esMedico() && $cita->medico_id !== $user->medico_id, 403);
        abort_if($user->esPaciente() && $cita->paciente_id !== $user->paciente_id, 403);

        $cita->load(['paciente', 'medico', 'especialidad', 'registradaPor', 'historial.usuario']);

        return view('citas.show', compact('cita'));
    }

    /** Médicos activos que atienden una especialidad (para el formulario). */
    public function medicosPorEspecialidad(Especialidad $especialidad): JsonResponse
    {
        return response()->json(
            $especialidad->medicos()->activos()->orderBy('apellidos')->get()
                ->map(fn ($m) => ['id' => $m->id, 'nombre' => $m->nombre_completo])->values()
        );
    }

    /** Horarios del médico en una fecha: solo se muestran los disponibles (RF06). */
    public function disponibilidad(Request $request, Medico $medico): JsonResponse
    {
        $request->validate(['fecha' => ['required', 'date', 'after_or_equal:today']]);

        return response()->json($this->agenda->bloques($medico, Carbon::parse($request->query('fecha'))));
    }
}
