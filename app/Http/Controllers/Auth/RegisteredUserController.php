<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Paciente;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Muestra la vista de registro.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Maneja una solicitud de registro entrante.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // 1. Creamos automáticamente el registro en la tabla 'pacientes' 
        // con valores predeterminados o provisionales para los campos obligatorios
        $paciente = Paciente::create([
            'nombres' => $request->name,
            'apellidos' => 'Registrado', 
            'ci' => 'CI-' . rand(10000, 99999), // Genera un carnet provisional único
            'fecha_nacimiento' => '2000-01-01',
            'telefono' => '70000000',
            'estado' => true,
        ]);

        // 2. Creamos el usuario vinculando el rol de paciente (rol_id = 3) y el paciente_id recién creado
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'rol_id' => 3, 
            'paciente_id' => $paciente->id,
        ]);

        event(new Registered($user));

        Auth::login($user);

        // 3. Redirigimos directamente al panel de citas del paciente
        return redirect(route('citas.index', absolute: false));
    }
}