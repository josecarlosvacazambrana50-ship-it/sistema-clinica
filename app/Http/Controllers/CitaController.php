<?php

namespace App\Http\Controllers;

use App\Http\Requests\CambiarEstadoRequest;
use App\Http\Requests\CancelarCitaRequest;
use App\Http\Requests\CitaRequest;
use App\Http\Requests\ReprogramarCitaRequest;
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

class CitaController extends Controller implements HasMiddleware
{
    public function __construct(private CitaService $citas, private AgendaService $agenda) {}

    public static function middleware(): array
    {
        return [
            new Middleware('permiso:citas,mostrar', only: ['index', 'show']),
            new Middleware('permiso:citas,reprogramar', only: ['formReprogramar', 'reprogramar']),
            new Middleware('permiso:citas,cancelar', only: ['cancelar']),
        ];
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Cita::with(['paciente', 'medico', 'especialidad']);

        if ($user->rol_id == 3 && $user->paciente_id) {
            $query->where('paciente_id', $user->paciente_id);
        } elseif ($user->medico_id) {
            $query->where('medico_id', $user->medico_id);
        }

        if ($request->filled('fecha')) {
            $query->whereDate('fecha', $request->fecha);
        }
        if ($request->filled('medico_id')) {
            $query->where('medico_id', $request->medico_id);
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $citas = $query->paginate(10);
        $medicos = Medico::all();

        return view('citas.index', compact('citas', 'medicos'));
    }

    public function create(Request $request): View
    {
        $user = $request->user();

        if ($user->rol_id != 3 && !$user->can('permiso', ['citas', 'crear'])) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        return view('citas.create', [
            'pacientes' => Paciente::activos()->orderBy('apellidos')->orderBy('nombres')->get(),
            'especialidades' => Especialidad::activas()->orderBy('nombre')->get(),
        ]);
    }

    public function store(CitaRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validated();

        $data['hora_inicio'] = $request->input('hora_inicio');
        $data['hora_fin'] = $request->input('hora_fin');

        // Captura explícita de la pasarela de pago simulada
        $data['metodo_pago'] = $request->input('metodo_pago', 'QR');
        $data['estado_pago'] = $request->input('estado_pago', 'Pagado');

        if ($user->rol_id == 3) {
            abort_unless($user->paciente_id, 403, 'No tienes un perfil de paciente asociado.');
            $data['paciente_id'] = $user->paciente_id;
        }

        $cita = $this->citas->programar($data, $user);

        return redirect()->route('citas.show', $cita)
            ->with('success', "¡Cita {$cita->codigo} registrada y pagada con éxito vía {$cita->metodo_pago}!");
    }

    public function show(Request $request, Cita $cita): View
    {
        $user = $request->user();
        abort_if($user->esMedico() && $cita->medico_id !== $user->medico_id, 403);
        abort_if($user->rol->nombre === 'Paciente' && $cita->paciente_id !== $user->paciente_id, 403);

        $cita->load(['paciente', 'medico', 'especialidad', 'registradaPor', 'historial.usuario']);

        return view('citas.show', compact('cita'));
    }

    public function formReprogramar(Cita $cita): View
    {
        abort_unless(in_array($cita->estado, ['programada', 'confirmada'], true), 403);
        $cita->load(['paciente', 'medico', 'especialidad']);

        return view('citas.reprogramar', compact('cita'));
    }

    public function reprogramar(ReprogramarCitaRequest $request, Cita $cita): RedirectResponse
    {
        $cita = $this->citas->reprogramar(
            $cita,
            $request->validated('fecha'),
            $request->validated('hora_inicio'),
            $request->user(),
            $request->validated('observacion'),
        );

        return redirect()->route('citas.show', $cita)
            ->with('success', "La cita {$cita->codigo} fue reprogramada correctamente.");
    }

    public function cancelar(CancelarCitaRequest $request, Cita $cita): RedirectResponse
    {
        $cita = $this->citas->cancelar($cita, $request->validated('motivo_cancelacion'), $request->user());

        return redirect()->route('citas.show', $cita)
            ->with('success', "La cita {$cita->codigo} fue cancelada correctamente.");
    }

    public function atender(Request $request, Cita $cita): RedirectResponse
    {
        $datos = $request->validate([
            'diagnostico' => ['required', 'string'],
            'receta' => ['required', 'string'],
            'observaciones' => ['nullable', 'string'],
        ]);

        $this->citas->atender($cita, $datos, $request->user());

        return redirect()->route('citas.show', $cita)
            ->with('success', '¡Consulta médica finalizada y receta guardada con éxito!');
    }

    public function cambiarEstado(CambiarEstadoRequest $request, Cita $cita): RedirectResponse
    {
        $cita = $this->citas->cambiarEstado(
            $cita,
            $request->validated('estado'),
            $request->user(),
            $request->validated('observacion'),
        );

        return redirect()->route('citas.show', $cita)
            ->with('success', "El estado de la cita {$cita->codigo} fue actualizado.");
    }

    public function medicosPorEspecialidad(Especialidad $especialidad): JsonResponse
    {
        return response()->json(
            $especialidad->medicos()->activos()->orderBy('apellidos')->orderBy('nombres')->get()
                ->map(fn ($m) => [
                    'id' => $m->id, 
                    'nombre' => "Dr(a). {$m->apellidos} {$m->nombres}"
                ])->values()
        );
    }

    public function disponibilidad(Request $request, Medico $medico): JsonResponse
    {
        $datos = $request->validate([
            'fecha' => ['required', 'date', 'after_or_equal:today'],
            'excluir_cita_id' => ['nullable', 'integer', 'exists:citas,id'],
        ]);
        $excluirCitaId = $datos['excluir_cita_id'] ?? null;

        if ($excluirCitaId) {
            $cita = Cita::findOrFail($excluirCitaId);
            abort_unless(
                $cita->medico_id === $medico->id && in_array($cita->estado, ['programada', 'confirmada'], true),
                403
            );
        }

        return response()->json($this->agenda->bloques($medico, Carbon::parse($datos['fecha']), $excluirCitaId));
    }
}