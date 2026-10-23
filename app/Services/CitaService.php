<?php

namespace App\Services;

use App\Models\Cita;
use App\Models\HistorialEstadoCita;
use App\Models\Medico;
use App\Models\Paciente;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Reglas de negocio de la programación de citas (RF06, RF07).
 */
class CitaService
{
    public function __construct(private AgendaService $agenda)
    {
    }

    /**
     * Programa una cita. Se vuelve a comprobar la disponibilidad dentro de una transacción
     * y, además, PostgreSQL rechaza cualquier superposición mediante la restricción ex_cita_sin_solape.
     *
     * @param  array{paciente_id:int, medico_id:int, especialidad_id:int, fecha:string, hora_inicio:string, hora_fin:string, motivo?:?string}  $datos
     */
    public function programar(array $datos, User $usuario): Cita
    {
        try {
            return DB::transaction(function () use ($datos, $usuario) {
                // Bloquea la fila del médico para serializar las reservas concurrentes
                $medico = Medico::whereKey($datos['medico_id'])->lockForUpdate()->firstOrFail();
                $paciente = Paciente::findOrFail($datos['paciente_id']);
                $fecha = Carbon::parse($datos['fecha']);

                if ($paciente->estado !== 'activo') {
                    $this->error('paciente_id', 'El paciente está inactivo.');
                }
                if ($medico->estado !== 'activo') {
                    $this->error('medico_id', 'El médico está inactivo y no puede recibir nuevas citas.');
                }
                if (! $medico->especialidades()->whereKey($datos['especialidad_id'])->exists()) {
                    $this->error('especialidad_id', 'El médico seleccionado no atiende esa especialidad.');
                }
                if ($fecha->lt(today())) {
                    $this->error('fecha', 'No se pueden registrar citas en fechas pasadas.');
                }
                if (! $this->agenda->estaLibre($medico, $fecha, $datos['hora_inicio'], $datos['hora_fin'])) {
                    $this->error('hora_inicio', 'El horario seleccionado no está disponible para este médico.');
                }

                $cita = Cita::create([
                    'paciente_id'     => $paciente->id,
                    'medico_id'       => $medico->id,
                    'especialidad_id' => $datos['especialidad_id'],
                    'registrada_por'  => $usuario->id,
                    'fecha'           => $fecha->toDateString(),
                    'hora_inicio'     => $datos['hora_inicio'],
                    'hora_fin'        => $datos['hora_fin'],
                    'motivo'          => $datos['motivo'] ?? null,
                    'estado'          => 'programada',
                ]);

                $cita->update(['codigo' => 'CM-' . str_pad((string) $cita->id, 4, '0', STR_PAD_LEFT)]);

                HistorialEstadoCita::create([
                    'cita_id'       => $cita->id,
                    'estado_nuevo'  => 'programada',
                    'user_id'       => $usuario->id,
                    'observacion'   => 'Cita registrada',
                ]);

                return $cita;
            });
        } catch (QueryException $e) {
            // 23P01 = exclusion_violation (otro usuario ocupó el horario al mismo tiempo)
            if ($e->getCode() === '23P01') {
                $this->error('hora_inicio', 'Otro usuario acaba de reservar ese horario. Elija otro.');
            }
            throw $e;
        }
    }

    private function error(string $campo, string $mensaje): never
    {
        throw ValidationException::withMessages([$campo => $mensaje]);
    }
}
