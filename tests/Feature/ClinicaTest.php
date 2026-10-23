<?php

use App\Models\Cita;
use App\Models\Especialidad;
use App\Models\HorarioAtencion;
use App\Models\Medico;
use App\Models\Paciente;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Pruebas mínimas del examen (sección 8). Se ejecutan contra PostgreSQL.
 */
beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->recepcion = User::where('email', 'recepcion@vidasalud.bo')->first();
    $this->admin = User::where('email', 'admin@vidasalud.bo')->first();
    $this->medico = Medico::where('nro_registro', 'MP-1001')->first();   // atiende lunes 08:00-12:00
    $this->especialidad = Especialidad::where('nombre', 'Medicina general')->first();
    $this->paciente = Paciente::where('ci', '6543210')->first();
    // Próximo lunes (el médico atiende lunes de 08:00 a 12:00, citas de 30 min)
    $this->lunes = now()->next('Monday')->toDateString();
});

function datosCita(array $extra = []): array
{
    return array_merge([
        'paciente_id'     => test()->paciente->id,
        'especialidad_id' => test()->especialidad->id,
        'medico_id'       => test()->medico->id,
        'fecha'           => test()->lunes,
        'hora_inicio'     => '09:00',
        'hora_fin'        => '09:30',
        'motivo'          => 'Consulta general',
    ], $extra);
}

function datosPaciente(array $extra = []): array
{
    return array_merge([
        'ci' => '9988776', 'nombres' => 'Carlos', 'apellidos' => 'Rojas Peña',
        'fecha_nacimiento' => '1985-06-01', 'telefono' => '76543210',
    ], $extra);
}

// 1
it('registra un paciente nuevo', function () {
    $this->actingAs($this->recepcion)->post('/pacientes', datosPaciente())->assertRedirect();

    $p = Paciente::where('ci', '9988776')->first();
    expect($p)->not->toBeNull()->and($p->codigo)->toStartWith('PAC-');
});

// 2
it('rechaza un paciente con CI duplicado', function () {
    $this->actingAs($this->recepcion)
        ->post('/pacientes', datosPaciente(['ci' => '6543210']))
        ->assertSessionHasErrors('ci');

    expect(Paciente::where('ci', '6543210')->count())->toBe(1);
});

// 3
it('programa una cita en un horario disponible', function () {
    $this->actingAs($this->recepcion)->post('/citas', datosCita())->assertSessionHasNoErrors()->assertRedirect();

    $cita = Cita::first();
    expect($cita->codigo)->toStartWith('CM-')->and($cita->estado)->toBe('programada');
    expect($cita->historial)->toHaveCount(1);
});

// 4
it('impide una segunda cita superpuesta para el mismo médico', function () {
    $this->actingAs($this->recepcion)->post('/citas', datosCita())->assertSessionHasNoErrors();

    $this->actingAs($this->recepcion)->post('/citas', datosCita())->assertSessionHasErrors('hora_inicio');

    expect(Cita::count())->toBe(1);
});

// 4b: la restricción de PostgreSQL protege incluso si se salta la validación del servidor
it('la base de datos rechaza citas superpuestas aunque se omita la validación', function () {
    $base = [
        'paciente_id' => $this->paciente->id, 'medico_id' => $this->medico->id,
        'especialidad_id' => $this->especialidad->id, 'registrada_por' => $this->recepcion->id,
        'fecha' => $this->lunes, 'hora_inicio' => '10:00', 'hora_fin' => '10:30',
    ];
    Cita::create($base);

    // SAVEPOINT: en PostgreSQL un error aborta la transacción de la prueba
    expect(fn () => DB::transaction(fn () => Cita::create($base)))->toThrow(Illuminate\Database\QueryException::class);

    // Una cita cancelada no ocupa horario (RN09)
    Cita::where('hora_inicio', '10:00')->update(['estado' => 'cancelada', 'motivo_cancelacion' => 'prueba']);
    Cita::create($base);
    expect(Cita::count())->toBe(2);
});

// 5
it('rechaza reservar fuera del horario del médico', function () {
    $this->actingAs($this->recepcion)
        ->post('/citas', datosCita(['hora_inicio' => '15:00', 'hora_fin' => '15:30']))
        ->assertSessionHasErrors('hora_inicio');

    expect(Cita::count())->toBe(0);
});

it('rechaza citas en fechas pasadas', function () {
    $this->actingAs($this->recepcion)
        ->post('/citas', datosCita(['fecha' => now()->subDay()->toDateString()]))
        ->assertSessionHasErrors('fecha');
});

it('no permite citas con un médico inactivo', function () {
    $this->medico->update(['estado' => 'inactivo']);

    $this->actingAs($this->recepcion)->post('/citas', datosCita())->assertSessionHasErrors('medico_id');
});

// 6 (el cambio de estado a cancelada se implementa en el Ciclo #2: aquí se prueba la regla de datos)
it('una cita cancelada conserva su registro y libera el horario', function () {
    $this->actingAs($this->recepcion)->post('/citas', datosCita());
    $cita = Cita::first();
    $cita->update(['estado' => 'cancelada', 'motivo_cancelacion' => 'El paciente avisó']);

    expect(Cita::count())->toBe(1);
    $this->actingAs($this->recepcion)->post('/citas', datosCita())->assertSessionHasNoErrors();
    expect(Cita::count())->toBe(2);
});

// 7
it('muestra el historial de citas de un paciente', function () {
    $this->actingAs($this->recepcion)->post('/citas', datosCita());
    $codigo = Cita::first()->codigo;

    $this->actingAs($this->recepcion)->get("/pacientes/{$this->paciente->id}")
        ->assertOk()->assertSee($codigo)->assertSee('Historial de citas');
});

// 8
it('busca las citas de un médico por fecha', function () {
    $this->actingAs($this->recepcion)->post('/citas', datosCita());
    $this->actingAs($this->recepcion)->post('/citas', datosCita(['hora_inicio' => '09:30', 'hora_fin' => '10:00']));
    [$a, $b] = Cita::orderBy('id')->pluck('codigo')->all();

    $this->actingAs($this->recepcion)
        ->get("/citas?fecha={$this->lunes}&medico_id={$this->medico->id}")
        ->assertOk()->assertSee($a)->assertSee($b);

    $this->actingAs($this->recepcion)
        ->get('/citas?fecha=' . now()->next('Monday')->addDays(1)->toDateString())
        ->assertOk()->assertDontSee($a);
});

// 9
it('niega el acceso a páginas internas sin iniciar sesión', function () {
    foreach (['/dashboard', '/pacientes', '/citas', '/citas/create', '/medicos', '/usuarios'] as $ruta) {
        $this->get($ruta)->assertRedirect('/login');
    }
});

it('impide a un rol sin permiso entrar a módulos de administración', function () {
    $this->actingAs($this->recepcion)->get('/usuarios')->assertForbidden();
    $this->actingAs($this->recepcion)->get('/especialidades/create')->assertForbidden();
});

// 10
it('el servidor valida y rechaza datos inválidos', function () {
    $this->actingAs($this->recepcion)
        ->post('/pacientes', datosPaciente(['fecha_nacimiento' => now()->addYear()->toDateString(), 'telefono' => 'abc', 'nombres' => '']))
        ->assertSessionHasErrors(['fecha_nacimiento', 'telefono', 'nombres']);

    $this->actingAs($this->admin)
        ->post('/medicos', ['nombres' => 'A', 'apellidos' => 'B', 'nro_registro' => 'X1'])
        ->assertSessionHasErrors('especialidades');
});

// Login (CU01)
it('inicia sesión con credenciales válidas y rechaza las incorrectas', function () {
    $this->post('/login', ['email' => 'admin@vidasalud.bo', 'password' => 'password'])->assertRedirect('/dashboard');
    $this->assertAuthenticated();
    $this->post('/logout')->assertRedirect('/login');
    $this->assertGuest();

    $this->post('/login', ['email' => 'admin@vidasalud.bo', 'password' => 'incorrecta'])->assertSessionHasErrors('email');
    $this->assertGuest();
});

it('no deja entrar a una cuenta desactivada', function () {
    $this->recepcion->update(['estado' => 'inactivo']);

    $this->post('/login', ['email' => 'recepcion@vidasalud.bo', 'password' => 'password'])->assertSessionHasErrors('email');
    $this->assertGuest();
});

it('guarda las contraseñas con hash', function () {
    expect(Hash::check('password', $this->admin->password))->toBeTrue()
        ->and($this->admin->password)->not->toBe('password');
});

// Especialidades y horarios
it('rechaza una especialidad repetida aunque cambien mayúsculas o tildes', function () {
    $this->actingAs($this->admin)->post('/especialidades', ['nombre' => 'PEDIATRÍA'])
        ->assertSessionHasErrors('nombre_normalizado');
});

it('valida que la hora final sea posterior a la inicial y que no se superponga', function () {
    $this->actingAs($this->admin)
        ->post("/medicos/{$this->medico->id}/horarios", ['dia_semana' => 2, 'hora_inicio' => '10:00', 'hora_fin' => '09:00', 'duracion_cita_min' => 30])
        ->assertSessionHasErrors('hora_fin');

    $this->actingAs($this->admin)
        ->post("/medicos/{$this->medico->id}/horarios", ['dia_semana' => 1, 'hora_inicio' => '11:00', 'hora_fin' => '13:00', 'duracion_cita_min' => 30])
        ->assertSessionHasErrors('hora_inicio');
});

it('el médico solo ve sus propias citas', function () {
    $this->actingAs($this->recepcion)->post('/citas', datosCita());
    $otro = Medico::where('nro_registro', 'MP-1002')->first();
    Cita::create([
        'paciente_id' => $this->paciente->id, 'medico_id' => $otro->id, 'especialidad_id' => $otro->especialidades->first()->id,
        'registrada_por' => $this->recepcion->id, 'fecha' => now()->next('Tuesday')->toDateString(),
        'hora_inicio' => '09:00', 'hora_fin' => '09:30', 'codigo' => 'CM-9999',
    ]);
    $propia = Cita::where('medico_id', $this->medico->id)->first()->codigo;
    $medicoUser = User::where('email', 'jperez@vidasalud.bo')->first();

    $this->actingAs($medicoUser)->get('/citas')->assertSee($propia)->assertDontSee('CM-9999');
});
