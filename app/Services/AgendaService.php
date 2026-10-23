<?php

namespace App\Services;

use App\Models\Cita;
use App\Models\Medico;
use Carbon\Carbon;

/**
 * Calcula los horarios disponibles de un médico (RF05, RF06).
 */
class AgendaService
{
    /**
     * Devuelve los bloques de atención de un médico en una fecha, indicando cuáles están libres.
     *
     * @return array<int, array{inicio:string, fin:string, libre:bool}>
     */
    public function bloques(Medico $medico, Carbon $fecha): array
    {
        // Un médico inactivo no recibe nuevas citas (RN04)
        if ($medico->estado !== 'activo') {
            return [];
        }

        $horarios = $medico->horarios()->activos()
            ->where('dia_semana', $fecha->isoWeekday())
            ->get();

        // Citas que ocupan horario ese día (las canceladas no cuentan, RN09)
        $ocupadas = Cita::where('medico_id', $medico->id)
            ->whereDate('fecha', $fecha->toDateString())
            ->ocupan()
            ->get(['hora_inicio', 'hora_fin']);

        $ahora = now();
        $bloques = [];

        foreach ($horarios as $h) {
            $inicio = Carbon::parse($fecha->toDateString() . ' ' . $h->hora_inicio);
            $limite = Carbon::parse($fecha->toDateString() . ' ' . $h->hora_fin);

            while ($inicio->copy()->addMinutes($h->duracion_cita_min)->lte($limite)) {
                $fin = $inicio->copy()->addMinutes($h->duracion_cita_min);

                $choca = $ocupadas->contains(function ($c) use ($inicio, $fin, $fecha) {
                    $ci = Carbon::parse($fecha->toDateString() . ' ' . $c->hora_inicio);
                    $cf = Carbon::parse($fecha->toDateString() . ' ' . $c->hora_fin);

                    return $inicio->lt($cf) && $fin->gt($ci);
                });

                $bloques[] = [
                    'inicio' => $inicio->format('H:i'),
                    'fin'    => $fin->format('H:i'),
                    // No se ofrecen horas pasadas (RN06)
                    'libre'  => ! $choca && $inicio->gt($ahora),
                ];

                $inicio = $fin;
            }
        }

        usort($bloques, fn ($a, $b) => strcmp($a['inicio'], $b['inicio']));

        return $bloques;
    }

    public function estaLibre(Medico $medico, Carbon $fecha, string $horaInicio, string $horaFin): bool
    {
        foreach ($this->bloques($medico, $fecha) as $b) {
            if ($b['inicio'] === substr($horaInicio, 0, 5) && $b['fin'] === substr($horaFin, 0, 5)) {
                return $b['libre'];
            }
        }

        return false; // fuera del horario de atención (RN05)
    }
}
