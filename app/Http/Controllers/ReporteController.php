<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReporteRequest;
use App\Services\ReporteService;
use Illuminate\View\View;

class ReporteController extends Controller
{
    public function __construct(private ReporteService $reportes) {}

    public function index(): View
    {
        return view('reportes.index', [
            'tipos' => ReporteService::TIPOS,
            'reporte' => null,
            'filtros' => [],
        ]);
    }

    public function mostrar(ReporteRequest $request, string $tipo): View
    {
        $reporte = $this->reportes->generar(
            $tipo,
            $request->validated('fecha_desde'),
            $request->validated('fecha_hasta'),
        );

        return view('reportes.index', [
            'tipos' => ReporteService::TIPOS,
            'tipoActual' => $tipo,
            'reporte' => $reporte,
            'filtros' => $request->validated(),
        ]);
    }
}
