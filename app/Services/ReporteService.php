<?php

namespace App\Services;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class ReporteService
{
    public const TIPOS = [
        'por-fecha' => 'Citas por fecha',
        'por-medico' => 'Citas por médico',
        'por-especialidad' => 'Citas por especialidad',
        'por-estado' => 'Citas por estado',
        'pacientes' => 'Citas por paciente',
        'resumen-periodo' => 'Resumen del período',
    ];

    public function generar(string $tipo, ?string $desde = null, ?string $hasta = null): array
    {
        abort_unless(array_key_exists($tipo, self::TIPOS), 404);

        $consulta = $this->filtrarFechas(DB::table('citas as c'), $desde, $hasta);
        $consultaEstados = $this->filtrarFechas(DB::table('citas as c'), $desde, $hasta);
        $porEstado = $consultaEstados->select('c.estado')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('c.estado')
            ->pluck('total', 'estado');

        $filas = match ($tipo) {
            'por-fecha' => $consulta->select('c.fecha as grupo', 'c.estado')
                ->selectRaw('COUNT(*) as total')->groupBy('c.fecha', 'c.estado')->orderBy('c.fecha')->get(),
            'por-medico' => $consulta->join('medicos as m', 'm.id', '=', 'c.medico_id')
                ->selectRaw("CONCAT(m.nombres, ' ', m.apellidos) as grupo, c.estado")
                ->selectRaw('COUNT(*) as total')->groupBy('m.nombres', 'm.apellidos', 'c.estado')
                ->orderBy('m.apellidos')->orderBy('m.nombres')->get(),
            'por-especialidad' => $consulta->join('especialidades as e', 'e.id', '=', 'c.especialidad_id')
                ->select('e.nombre as grupo', 'c.estado')->selectRaw('COUNT(*) as total')
                ->groupBy('e.nombre', 'c.estado')->orderBy('e.nombre')->get(),
            'por-estado', 'resumen-periodo' => $consulta->select('c.estado as grupo', 'c.estado')
                ->selectRaw('COUNT(*) as total')->groupBy('c.estado')->orderBy('c.estado')->get(),
            'pacientes' => $consulta->join('pacientes as p', 'p.id', '=', 'c.paciente_id')
                ->selectRaw("CONCAT(p.nombres, ' ', p.apellidos) as grupo, c.estado")
                ->selectRaw('COUNT(*) as total')->groupBy('p.nombres', 'p.apellidos', 'c.estado')
                ->orderBy('p.apellidos')->orderBy('p.nombres')->get(),
        };

        $totales = collect(array_fill_keys(['programada', 'confirmada', 'atendida', 'cancelada', 'inasistencia'], 0))
            ->merge($porEstado->map(fn ($total) => (int) $total));

        return [
            'titulo' => self::TIPOS[$tipo],
            'filas' => $filas,
            'totalCitas' => $totales->sum(),
            'totalesPorEstado' => $totales,
        ];
    }

    private function filtrarFechas(Builder $consulta, ?string $desde, ?string $hasta): Builder
    {
        return $consulta
            ->when($desde, fn ($q) => $q->whereDate('c.fecha', '>=', $desde))
            ->when($hasta, fn ($q) => $q->whereDate('c.fecha', '<=', $hasta));
    }
}
