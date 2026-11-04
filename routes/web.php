<?php

use App\Http\Controllers\AgendaController;
use App\Http\Controllers\BitacoraController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EspecialidadController;
use App\Http\Controllers\HistorialController;
use App\Http\Controllers\HorarioController;
use App\Http\Controllers\MedicoController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\PerfilPacienteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;

Route::redirect('/', '/login');

// ==========================================
// RUTAS PARA USUARIOS NO AUTENTICADOS (INVITADOS)
// ==========================================
Route::middleware('guest')->group(function () {
    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');

    Route::post('/register', function (Request $request) {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'rol_id' => 3, // Rol de paciente
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    });
});

// ==========================================
// RUTAS INTERNAS (Exigen sesión válida - RF01)
// ==========================================
Route::middleware('auth')->group(function () {

    // Perfil de la cuenta
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('mi-perfil', [PerfilPacienteController::class, 'mostrar'])
        ->middleware('permiso:perfil,editar')->name('perfil.paciente.show');
    Route::put('mi-perfil', [PerfilPacienteController::class, 'actualizar'])
        ->middleware('permiso:perfil,editar')->name('perfil.paciente.update');
    Route::put('mi-perfil/password', [PerfilPacienteController::class, 'actualizarContrasena'])
        ->middleware('permiso:perfil,editar')->name('perfil.paciente.password');

    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('verified')->name('dashboard');
    Route::get('agenda', [AgendaController::class, 'index'])
        ->middleware('permiso:agenda,ver')->name('agenda.index');
    Route::get('reportes', [ReporteController::class, 'index'])
        ->middleware('permiso:reportes,ver')->name('reportes.index');
    Route::get('reportes/{tipo}', [ReporteController::class, 'mostrar'])
        ->middleware('permiso:reportes,ver')->name('reportes.mostrar');

    // CU03 - Usuarios, roles y permisos
    Route::resource('usuarios', UsuarioController::class)->except(['show', 'destroy'])
        ->parameters(['usuarios' => 'usuario']);
    Route::patch('usuarios/{usuario}/estado', [UsuarioController::class, 'cambiarEstado'])->name('usuarios.estado');

    Route::get('roles', [RolController::class, 'index'])->name('roles.index');
    Route::get('roles/{rol}/permisos', [RolController::class, 'permisos'])->name('roles.permisos');
    Route::put('roles/{rol}/permisos', [RolController::class, 'actualizarPermisos'])->name('roles.permisos.update');

    // CU04 - Especialidades
    Route::resource('especialidades', EspecialidadController::class)->except(['show', 'destroy'])
        ->parameters(['especialidades' => 'especialidad']);
    Route::patch('especialidades/{especialidad}/estado', [EspecialidadController::class, 'cambiarEstado'])
        ->name('especialidades.estado');

    // CU05 - Médicos
    Route::resource('medicos', MedicoController::class)->except(['destroy'])
        ->parameters(['medicos' => 'medico']);
    Route::patch('medicos/{medico}/estado', [MedicoController::class, 'cambiarEstado'])->name('medicos.estado');

    // CU06 - Horarios de atención
    Route::get('medicos/{medico}/horarios', [HorarioController::class, 'index'])->name('horarios.index');
    Route::post('medicos/{medico}/horarios', [HorarioController::class, 'store'])->name('horarios.store');
    Route::put('medicos/{medico}/horarios/{horario}', [HorarioController::class, 'update'])->name('horarios.update');
    Route::patch('medicos/{medico}/horarios/{horario}/estado', [HorarioController::class, 'cambiarEstado'])
        ->name('horarios.estado');
    Route::delete('medicos/{medico}/horarios/{horario}', [HorarioController::class, 'destroy'])
        ->name('horarios.destroy');

    // CU07 - Pacientes
    Route::get('mi-historial', [HistorialController::class, 'propio'])->name('mi.historial');
    Route::get('pacientes/{paciente}/historial', [HistorialController::class, 'dePaciente'])
        ->middleware('permiso:historial,ver')->name('pacientes.historial');
    Route::resource('pacientes', PacienteController::class)->except(['destroy'])
        ->parameters(['pacientes' => 'paciente']);
    Route::patch('pacientes/{paciente}/estado', [PacienteController::class, 'cambiarEstado'])->name('pacientes.estado');

    // CU08 - Citas médicas (Rutas AJAX y de acción)
    Route::get('citas/medicos/{especialidad}', [CitaController::class, 'medicosPorEspecialidad'])->name('citas.medicos');
    Route::get('citas/medicos-por-especialidad/{especialidad}', [CitaController::class, 'medicosPorEspecialidad']); // Ruta alias de respaldo
    Route::get('citas/disponibilidad/{medico}', [CitaController::class, 'disponibilidad'])->name('citas.disponibilidad');
    Route::get('citas/{cita}/reprogramar', [CitaController::class, 'formReprogramar'])->name('citas.reprogramar.form');
    Route::put('citas/{cita}/reprogramar', [CitaController::class, 'reprogramar'])->name('citas.reprogramar');
    Route::post('citas/{cita}/cancelar', [CitaController::class, 'cancelar'])->name('citas.cancelar');
    Route::post('citas/{cita}/estado', [CitaController::class, 'cambiarEstado'])->name('citas.estado');
    
    // CORREGIDO: Apunta directo al método atender de CitaController
    Route::post('citas/{cita}/atender', [CitaController::class, 'atender'])->name('citas.atender');
    
    Route::resource('citas', CitaController::class)->only(['index', 'create', 'store', 'show']);

    Route::get('bitacora', [BitacoraController::class, 'index'])
        ->middleware('permiso:bitacora,mostrar')->name('bitacora.index');
});

require __DIR__.'/auth.php';