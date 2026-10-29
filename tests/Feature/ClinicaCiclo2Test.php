<?php

use App\Models\Cita;
use App\Models\Especialidad;
use App\Models\Medico;
use App\Models\Paciente;
use App\Models\User;
use App\Services\ReporteService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\Hash;

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

    $cita->update(['codigo' => 'CM-'.str_pad((string) $cita->id, 4, '0', STR_PAD_LEFT)]);

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
        ->and($cita->codigo)->toBe('CM-'.str_pad((string) $cita->id, 4, '0', STR_PAD_LEFT))
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

it('solo acepta una reprogramación cuando dos citas compiten por el mismo horario', function () {
    $primera = crearCitaCiclo2();
    $segunda = crearCitaCiclo2(['hora_inicio' => '09:30', 'hora_fin' => '10:00']);

    $this->actingAs($this->recepcion)
        ->put(route('citas.reprogramar', $primera), ['fecha' => $this->lunes, 'hora_inicio' => '10:30'])
        ->assertRedirect(route('citas.show', $primera));

    $this->actingAs($this->recepcion)
        ->put(route('citas.reprogramar', $segunda), ['fecha' => $this->lunes, 'hora_inicio' => '10:30'])
        ->assertSessionHasErrors('hora_inicio');

    expect(Cita::whereDate('fecha', $this->lunes)->where('hora_inicio', '10:30')->count())->toBe(1);
});

it('rechaza cancelar una cita sin motivo', function () {
    $cita = crearCitaCiclo2();

    $this->actingAs($this->recepcion)
        ->post(route('citas.cancelar', $cita), ['motivo_cancelacion' => ''])
        ->assertSessionHasErrors('motivo_cancelacion');

    expect($cita->fresh()->estado)->toBe('programada');
});

it('cancela sin eliminar la cita y libera su horario', function () {
    $cita = crearCitaCiclo2();

    $this->actingAs($this->recepcion)
        ->post(route('citas.cancelar', $cita), ['motivo_cancelacion' => 'El paciente solicitó cancelar'])
        ->assertRedirect(route('citas.show', $cita));

    expect($cita->fresh()->estado)->toBe('cancelada')
        ->and($cita->fresh()->motivo_cancelacion)->toBe('El paciente solicitó cancelar')
        ->and(Cita::count())->toBe(1)
        ->and($cita->historial)->toHaveCount(1);

    $nueva = crearCitaCiclo2();
    expect($nueva->id)->not->toBe($cita->id)
        ->and(Cita::count())->toBe(2);
});

it('rechaza marcar como atendida una cita cancelada', function () {
    $cita = crearCitaCiclo2(['estado' => 'cancelada', 'motivo_cancelacion' => 'Anulada']);

    $this->actingAs($this->recepcion)
        ->post(route('citas.estado', $cita), ['estado' => 'atendida'])
        ->assertSessionHasErrors('estado');

    expect($cita->fresh()->estado)->toBe('cancelada');
});

it('rechaza registrar asistencia antes de la fecha de la cita', function () {
    $cita = crearCitaCiclo2();

    $this->actingAs($this->recepcion)
        ->post(route('citas.estado', $cita), ['estado' => 'inasistencia'])
        ->assertSessionHasErrors('estado');

    expect($cita->fresh()->estado)->toBe('programada');
});

it('impide que un médico cambie el estado de la cita de otro médico', function () {
    $cita = crearCitaCiclo2();
    $usuarioMedico = User::where('email', 'jperez@vidasalud.bo')->firstOrFail();
    $otroMedico = Medico::where('nro_registro', 'MP-1002')->firstOrFail();

    $cita->update(['medico_id' => $otroMedico->id]);

    $this->actingAs($usuarioMedico)
        ->post(route('citas.estado', $cita), ['estado' => 'atendida'])
        ->assertForbidden();

    expect($cita->fresh()->estado)->toBe('programada');
});

it('registra el cambio de estado en el historial', function () {
    $cita = crearCitaCiclo2();
    $this->travelTo(now()->next('Monday')->startOfDay());

    $this->actingAs($this->recepcion)
        ->post(route('citas.estado', $cita), ['estado' => 'atendida', 'observacion' => 'Atención concluida'])
        ->assertRedirect(route('citas.show', $cita));

    expect($cita->fresh()->estado)->toBe('atendida')
        ->and($cita->historial)->toHaveCount(1)
        ->and($cita->historial->first()->observacion)->toBe('Atención concluida');
});

it('muestra solo las acciones de estado válidas y la cancelación con motivo', function () {
    $cita = crearCitaCiclo2();

    $this->actingAs($this->recepcion)
        ->get(route('citas.show', $cita))
        ->assertOk()
        ->assertSee('Confirmar cita')
        ->assertSee('Cancelar cita')
        ->assertDontSee('name="estado" value="cancelada"', false);
});

it('filtra la agenda por médico, fecha y estado', function () {
    $programada = crearCitaCiclo2(['estado' => 'programada']);
    $confirmada = crearCitaCiclo2(['hora_inicio' => '09:30', 'hora_fin' => '10:00', 'estado' => 'confirmada']);

    $this->actingAs($this->recepcion)
        ->get(route('agenda.index', [
            'medico_id' => $this->medico->id,
            'fecha' => $this->lunes,
            'estado' => 'confirmada',
        ]))
        ->assertOk()
        ->assertSee($confirmada->codigo)
        ->assertDontSee($programada->codigo);
});

it('fuerza a los médicos a consultar solo su propia agenda', function () {
    $usuarioMedico = User::where('email', 'jperez@vidasalud.bo')->firstOrFail();
    $otroMedico = Medico::where('nro_registro', 'MP-1002')->firstOrFail();
    $cita = crearCitaCiclo2(['medico_id' => $otroMedico->id]);

    $this->actingAs($usuarioMedico)
        ->get(route('agenda.index', ['medico_id' => $otroMedico->id, 'fecha' => $this->lunes]))
        ->assertOk()
        ->assertSee('Sin citas programadas')
        ->assertDontSee($cita->codigo);
});

it('permite al paciente ver solo su propio historial', function () {
    $otraPersona = Paciente::create([
        'ci' => '12345678', 'nombres' => 'Otra', 'apellidos' => 'Persona',
        'fecha_nacimiento' => '1980-01-01', 'telefono' => '71234567',
    ]);

    $usuarioPaciente = User::where('email', 'maria@correo.com')->firstOrFail();
    $this->actingAs($usuarioPaciente)
        ->get(route('pacientes.historial', $otraPersona))
        ->assertForbidden();
});

it('filtra el historial por estado y rango de fechas', function () {
    $cita = crearCitaCiclo2(['estado' => 'confirmada']);

    $this->actingAs($this->recepcion)
        ->get(route('pacientes.historial', [
            'paciente' => $this->paciente->id,
            'estado' => 'confirmada',
            'fecha_desde' => $this->lunes,
            'fecha_hasta' => $this->lunes,
        ]))
        ->assertOk()
        ->assertSee('Confirmada');

    $this->actingAs($this->recepcion)
        ->get(route('pacientes.historial', [
            'paciente' => $this->paciente->id,
            'fecha_desde' => $this->lunes,
            'fecha_hasta' => now()->toDateString(),
        ]))
        ->assertSessionHasErrors('fecha_hasta');
});

it('genera totales correctos en un reporte por período', function () {
    crearCitaCiclo2(['estado' => 'atendida']);
    crearCitaCiclo2([
        'hora_inicio' => '09:30', 'hora_fin' => '10:00',
        'estado' => 'cancelada', 'motivo_cancelacion' => 'Cancelada para prueba',
    ]);
    crearCitaCiclo2(['hora_inicio' => '10:00', 'hora_fin' => '10:30', 'estado' => 'inasistencia']);

    $this->actingAs($this->recepcion)
        ->get(route('reportes.mostrar', [
            'tipo' => 'resumen-periodo',
            'fecha_desde' => $this->lunes,
            'fecha_hasta' => $this->lunes,
        ]))
        ->assertOk()
        ->assertSee('Resumen del período')
        ->assertSee('Atendida')
        ->assertSee('Cancelada')
        ->assertSee('Inasistencia');

    $reporte = app(ReporteService::class)->generar('resumen-periodo', $this->lunes, $this->lunes);
    expect($reporte['totalCitas'])->toBe(3)
        ->and($reporte['totalesPorEstado']['atendida'])->toBe(1)
        ->and($reporte['totalesPorEstado']['cancelada'])->toBe(1)
        ->and($reporte['totalesPorEstado']['inasistencia'])->toBe(1);
});

it('rechaza un rango de fechas invertido en reportes', function () {
    $this->actingAs($this->recepcion)
        ->get(route('reportes.mostrar', [
            'tipo' => 'por-fecha',
            'fecha_desde' => $this->lunes,
            'fecha_hasta' => now()->toDateString(),
        ]))
        ->assertSessionHasErrors('fecha_hasta');
});

it('permite al paciente actualizar su perfil y registra la bitácora', function () {
    $usuarioPaciente = User::where('email', 'maria@correo.com')->firstOrFail();

    $this->actingAs($usuarioPaciente)
        ->put(route('perfil.paciente.update'), [
            'ci' => '00000000',
            'codigo' => 'PAC-9999',
            'estado' => 'inactivo',
            'nombres' => 'María Elena',
            'apellidos' => 'López Rivera',
            'fecha_nacimiento' => '1990-04-12',
            'sexo' => 'F',
            'telefono' => '70123456',
            'correo' => 'maria.actualizada@correo.com',
            'direccion' => 'Av. Nueva 123',
            'tipo_sangre' => 'O+',
            'alergias' => 'Polen',
        ])
        ->assertRedirect(route('perfil.paciente.show'));

    $paciente = $usuarioPaciente->fresh()->paciente;
    expect($paciente->nombres)->toBe('María Elena')
        ->and($paciente->ci)->toBe('6543210')
        ->and($paciente->codigo)->toBe('PAC-0001')
        ->and($paciente->estado)->toBe('activo')
        ->and($usuarioPaciente->fresh()->email)->toBe('maria.actualizada@correo.com');

    $this->assertDatabaseHas('bitacoras', [
        'user_id' => $usuarioPaciente->id,
        'tabla_afectada' => 'pacientes',
        'registro_id' => $paciente->id,
        'accion' => 'editar',
    ]);
});

it('ignora cambios de CI enviados desde el perfil del paciente', function () {
    $usuarioPaciente = User::where('email', 'maria@correo.com')->firstOrFail();

    $this->actingAs($usuarioPaciente)
        ->put(route('perfil.paciente.update'), [
            'ci' => '00000000',
            'nombres' => $usuarioPaciente->paciente->nombres,
            'apellidos' => $usuarioPaciente->paciente->apellidos,
            'fecha_nacimiento' => $usuarioPaciente->paciente->fecha_nacimiento->toDateString(),
            'telefono' => $usuarioPaciente->paciente->telefono,
        ])
        ->assertRedirect(route('perfil.paciente.show'));

    expect($usuarioPaciente->fresh()->paciente->ci)->toBe('6543210');
});

it('avisa cuando la cuenta paciente no tiene un perfil vinculado', function () {
    $cita = crearCitaCiclo2();
    $usuarioPaciente = User::create([
        'email' => 'sin-perfil@correo.com',
        'password' => 'password',
        'rol_id' => User::where('email', 'maria@correo.com')->value('rol_id'),
    ]);

    $this->actingAs($usuarioPaciente)
        ->get(route('perfil.paciente.show'))
        ->assertOk()
        ->assertSee('Contacta a recepción');

    $this->actingAs($usuarioPaciente)->get(route('citas.index'))->assertDontSee($cita->codigo);
    $this->actingAs($usuarioPaciente)->get(route('citas.show', $cita))->assertForbidden();
});

it('valida la contraseña actual y guarda el hash de la nueva contraseña', function () {
    $usuarioPaciente = User::where('email', 'maria@correo.com')->firstOrFail();

    $this->actingAs($usuarioPaciente)
        ->put(route('perfil.paciente.password'), [
            'current_password' => 'incorrecta',
            'password' => 'nueva-clave-segura',
            'password_confirmation' => 'nueva-clave-segura',
        ])
        ->assertSessionHasErrorsIn('updatePassword', 'current_password');

    $this->actingAs($usuarioPaciente)
        ->put(route('perfil.paciente.password'), [
            'current_password' => 'password',
            'password' => 'nueva-clave-segura',
            'password_confirmation' => 'nueva-clave-segura',
        ])
        ->assertRedirect(route('perfil.paciente.show'));

    expect(Hash::check('nueva-clave-segura', $usuarioPaciente->fresh()->password))->toBeTrue();
});
