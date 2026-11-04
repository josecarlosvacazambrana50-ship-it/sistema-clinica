<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cita;
use App\Services\CitaService;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class HistorialController extends Controller implements HasMiddleware
{
    public function __construct(private CitaService $citasService) {}

    public static function middleware(): array
    {
        return [
            // Permitimos el acceso libre a usuarios autenticados para que los pacientes vean su historial
            new Middleware('auth'),
        ];
    }

    /**
     * Muestra el historial médico y recetas del paciente autenticado.
     */
    public function propio(Request $request): View
    {
        $user = $request->user();

        // Preparamos la consulta para listar las citas que ya fueron atendidas
        $query = Cita::with(['medico', 'especialidad', 'historial'])
            ->where('estado', 'atendida');

        // Si es un paciente (rol_id = 3), filtramos estrictamente por su propio ID de paciente
        if ($user->rol_id == 3) {
            abort_unless($user->paciente_id, 403, 'No tienes un perfil de paciente asociado.');
            $query->where('paciente_id', $user->paciente_id);
        }

        $citasAtendidas = $query->orderBy('fecha', 'desc')->paginate(10);

        return view('historial.mi-historial', compact('citasAtendidas'));
    }

    public function store(Request $request, Cita $cita)
    {
        $request->validate([
            'diagnostico' => 'required|string',
            'receta' => 'required|string',
            'observaciones' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Consolidamos el diagnóstico, la receta y las notas en un formato claro para el historial
            $detalleAtencion = "Diagnóstico: " . $request->diagnostico . 
                               " | Receta: " . $request->receta . 
                               ($request->filled('observaciones') ? " | Notas: " . $request->observaciones : "");

            // Utilizamos tu servicio oficial para cambiar el estado a 'atendida' y registrar la atención
            $this->citasService->cambiarEstado(
                $cita,
                'atendida',
                $request->user(),
                $detalleAtencion
            );

            DB::commit();

            return redirect()->route('citas.index')
                ->with('success', '¡Consulta médica finalizada y receta guardada con éxito!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Ocurrió un error al guardar: ' . $e->getMessage()]);
        }
    }
}