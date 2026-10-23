<?php

use App\Http\Controllers\BitacoraController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EspecialidadController;
use App\Http\Controllers\HorarioController;
use App\Http\Controllers\MedicoController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

// Todas las rutas internas exigen una sesión válida (RF01)
Route::middleware('auth')->group(function () {

    // Perfil de la cuenta
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('verified')->name('dashboard');

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

    // CU07 - Pacientes
    Route::resource('pacientes', PacienteController::class)->except(['destroy'])
        ->parameters(['pacientes' => 'paciente']);
    Route::patch('pacientes/{paciente}/estado', [PacienteController::class, 'cambiarEstado'])->name('pacientes.estado');

    // CU08 - Citas médicas
    Route::get('citas/medicos/{especialidad}', [CitaController::class, 'medicosPorEspecialidad'])->name('citas.medicos');
    Route::get('citas/disponibilidad/{medico}', [CitaController::class, 'disponibilidad'])->name('citas.disponibilidad');
    Route::resource('citas', CitaController::class)->only(['index', 'create', 'store', 'show']);

    Route::get('bitacora', [BitacoraController::class, 'index'])
        ->middleware('permiso:bitacora,mostrar')->name('bitacora.index');
});

require __DIR__ . '/auth.php';
