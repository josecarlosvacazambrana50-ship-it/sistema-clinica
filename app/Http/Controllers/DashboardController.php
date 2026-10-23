<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Medico;
use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $proximas = Cita::with(['paciente', 'medico', 'especialidad'])
            ->when($user->esMedico(), fn ($q) => $q->where('medico_id', $user->medico_id))
            ->when($user->esPaciente(), fn ($q) => $q->where('paciente_id', $user->paciente_id))
            ->whereDate('fecha', '>=', today())->ocupan()
            ->orderBy('fecha')->orderBy('hora_inicio')->limit(6)->get();

        $resumen = [
            'citas_hoy'  => Cita::whereDate('fecha', today())->ocupan()
                ->when($user->esMedico(), fn ($q) => $q->where('medico_id', $user->medico_id))->count(),
            'pacientes'  => Paciente::activos()->count(),
            'medicos'    => Medico::activos()->count(),
            'inasist'    => Cita::where('estado', 'inasistencia')->whereMonth('fecha', now()->month)->count(),
        ];

        return view('dashboard', compact('proximas', 'resumen'));
    }
}
