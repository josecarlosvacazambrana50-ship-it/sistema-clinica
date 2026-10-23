<?php

namespace App\Http\Controllers;

use App\Http\Requests\UsuarioRequest;
use App\Models\Medico;
use App\Models\Paciente;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

/** CU03 - Gestionar Usuarios y Roles */
class UsuarioController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permiso:usuarios,mostrar', only: ['index']),
            new Middleware('permiso:usuarios,crear', only: ['create', 'store']),
            new Middleware('permiso:usuarios,modificar', only: ['edit', 'update', 'cambiarEstado']),
        ];
    }

    public function index(Request $request): View
    {
        $buscar = trim((string) $request->query('q'));

        $usuarios = User::with('rol')
            ->when($buscar, fn ($q) => $q->whereRaw('lower(email) like ?', ['%' . mb_strtolower($buscar) . '%']))
            ->orderBy('email')->paginate(10)->withQueryString();

        return view('usuarios.index', compact('usuarios', 'buscar'));
    }

    public function create(): View
    {
        return view('usuarios.create', $this->catalogos());
    }

    public function store(UsuarioRequest $request): RedirectResponse
    {
        User::create($this->datos($request) + ['estado' => 'activo']);

        return redirect()->route('usuarios.index')->with('success', 'Usuario registrado.');
    }

    public function edit(User $usuario): View
    {
        return view('usuarios.edit', ['usuario' => $usuario] + $this->catalogos());
    }

    public function update(UsuarioRequest $request, User $usuario): RedirectResponse
    {
        $datos = $this->datos($request);
        if (empty($datos['password'])) {
            unset($datos['password']);
        }
        $usuario->update($datos);

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado.');
    }

    public function cambiarEstado(Request $request, User $usuario): RedirectResponse
    {
        if ($usuario->id === $request->user()->id) {
            return back()->with('error', 'No puede desactivar su propia cuenta.');
        }
        $usuario->update(['estado' => $usuario->estado === 'activo' ? 'inactivo' : 'activo']);

        return back()->with('success', 'Estado del usuario actualizado.');
    }

    private function datos(UsuarioRequest $request): array
    {
        $d = $request->validated();
        $rol = Rol::find($d['rol_id'])->nombre;
        // Solo se vincula el médico o el paciente que corresponde al rol elegido
        $d['medico_id'] = $rol === 'Médico' ? ($d['medico_id'] ?? null) : null;
        $d['paciente_id'] = $rol === 'Paciente' ? ($d['paciente_id'] ?? null) : null;

        return $d;
    }

    private function catalogos(): array
    {
        return [
            'roles'     => Rol::orderBy('id')->get(),
            'medicos'   => Medico::activos()->orderBy('apellidos')->get(),
            'pacientes' => Paciente::activos()->orderBy('apellidos')->get(),
        ];
    }
}
