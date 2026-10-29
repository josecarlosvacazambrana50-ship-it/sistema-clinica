<?php

namespace App\Http\Controllers;

use App\Http\Requests\PerfilPacienteRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PerfilPacienteController extends Controller
{
    public function mostrar(Request $request): View
    {
        $paciente = $request->user()->rol->nombre === 'Paciente'
            ? $request->user()->paciente
            : null;

        return view('perfil.paciente', compact('paciente'));
    }

    public function actualizar(PerfilPacienteRequest $request): RedirectResponse
    {
        $usuario = $request->user();
        $paciente = $usuario->paciente;
        abort_if(! $paciente, 403);
        $datos = $request->validated();

        DB::transaction(function () use ($usuario, $paciente, $datos) {
            $paciente->update($datos);

            if (! empty($datos['correo']) && $usuario->email !== $datos['correo']) {
                $usuario->email = $datos['correo'];
                $usuario->save();
            }
        });

        return redirect()->route('perfil.paciente.show')->with('success', 'Tu perfil fue actualizado correctamente.');
    }

    public function actualizarContrasena(Request $request): RedirectResponse
    {
        abort_unless($request->user()->rol->nombre === 'Paciente', 403);

        $datos = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $request->user()->update(['password' => $datos['password']]);

        return redirect()->route('perfil.paciente.show')->with('status', 'password-updated');
    }
}
