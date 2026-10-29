<?php

use App\Models\Cita;
use App\Models\Especialidad;
use App\Models\Medico;
use App\Models\Paciente;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->recepcion = User::where('email', 'recepcion@vidasalud.bo')->firstOrFail();
    $this->medico = Medico::where('nro_registro', 'MP-1001')->firstOrFail();
    $this->especialidad = Especialidad::where('nombre', 'Medicina general')->firstOrFail();
    $this->paciente = Paciente::where('ci', '6543210')->firstOrFail();
    $this->lunes = now()->next('Monday')->toDateString();
});

function crearCitaCiclo2(array $sobrescrituras = []): Cita
{
    $cita = Cita::create(array_merge([
        'paciente_id' => test()->paciente->id,
        'medico_id' => test()->medico->id,
        'especialidad_id' => test()->especialidad->id,
        'registrada_por' => test()->recepcion->id,
        'fecha' => test()->lunes,
        'hora_inicio' => '09:00',
        'hora_fin' => '09:30',
        'estado' => 'programada',
    ], $sobrescrituras));

    $cita->update(['codigo' => 'CM-' . str_pad((string) $cita->id, 4, '0', STR_PAD_LEFT)]);

    return $cita;
}

it('reprograma una cita a un bloque libre y registra el cambio', function () {
    $cita = crearCitaCiclo2();

    $this->actingAs($this->recepcion)
        ->put(route('citas.reprogramar', $cita), ['fecha' => $this->lunes, 'hora_inicio' => '09:30'])
        ->assertRedirect(route('citas.show', $cita));

    $cita->refresh();
    expect($cita->fecha->toDateString())->toBe($this->lunes)
        ->and($cita->hora_inicio_corta)->toBe('09:30')
        ->and($cita->hora_fin_corta)->toBe('10:00')
        ->and($cita->codigo)->toBe('CM-' . str_pad((string) $cita->id, 4, '0', STR_PAD_LEFT))
        ->and($cita->historial)->toHaveCount(1);
});

it('rechaza reprogramar una cita a un bloque ocupado', function () {
    $cita = crearCitaCiclo2();
    crearCitaCiclo2(['hora_inicio' => '09:30', 'hora_fin' => '10:00']);

    $this->actingAs($this->recepcion)
        ->put(route('citas.reprogramar', $cita), ['fecha' => $this->lunes, 'hora_inicio' => '09:30'])
        ->assertSessionHasErrors('hora_inicio');

    expect($cita->fresh()->hora_inicio_corta)->toBe('09:00');
});

it('no permite reprogramar una cita en estado final', function () {
    $cita = crearCitaCiclo2(['estado' => 'atendida']);

    $this->actingAs($this->recepcion)
        ->put(route('citas.reprogramar', $cita), ['fecha' => $this->lunes, 'hora_inicio' => '09:30'])
        ->assertSessionHasErrors('cita');

    expect($cita->fresh()->hora_inicio_corta)->toBe('09:00');
});
