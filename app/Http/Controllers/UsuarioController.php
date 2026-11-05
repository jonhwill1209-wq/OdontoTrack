<?php

namespace App\Http\Controllers;

use App\Enums\Rol;
use App\Http\Requests\StoreUsuarioRequest;
use App\Http\Requests\UpdateUsuarioRequest;
use App\Models\Usuario;
use App\Services\UsuarioService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UsuarioController extends Controller
{
    public function __construct(
        private readonly UsuarioService $usuarioService
    ) {
    }

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Usuario::class);

        $filtros = $request->validate([
            'buscar' => ['nullable', 'string', 'max:100'],
            'rol' => ['nullable', 'string', Rule::in(Rol::valores())],
            'estado' => ['nullable', 'in:0,1,todos'],
        ], [
            'buscar.string' => 'La búsqueda debe ser texto.',
            'buscar.max' => 'La búsqueda no debe superar 100 caracteres.',
            'rol.string' => 'Selecciona un rol válido.',
            'rol.in' => 'Selecciona un rol válido.',
            'estado.in' => 'Selecciona un estado válido.',
        ]);

        return view('usuarios.index', [
            'usuarios' => $this->usuarioService->listar($filtros),
            'totales' => $this->usuarioService->totales(),
            'roles' => Rol::cases(),
            'buscar' => $filtros['buscar'] ?? '',
            'rol' => $filtros['rol'] ?? '',
            'estado' => $filtros['estado'] ?? '1',
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', Usuario::class);

        return view('usuarios.create', [
            'usuario' => new Usuario(),
            'roles' => Rol::cases(),
        ]);
    }

    public function store(StoreUsuarioRequest $request): RedirectResponse
    {
        $usuario = $this->usuarioService->crear($request->validated());

        return redirect()
            ->route('usuarios.show', $usuario)
            ->with('exito', 'Usuario creado correctamente. Ya puede iniciar sesión con su correo.');
    }

    public function show(Usuario $usuario): View
    {
        Gate::authorize('view', $usuario);

        return view('usuarios.show', [
            'usuario' => $usuario->load('odontologo'),
        ]);
    }

    public function edit(Usuario $usuario): View
    {
        Gate::authorize('update', $usuario);

        return view('usuarios.edit', compact('usuario'));
    }

    public function update(UpdateUsuarioRequest $request, Usuario $usuario): RedirectResponse
    {
        $this->usuarioService->actualizar($usuario, $request->validated());

        return redirect()
            ->route('usuarios.show', $usuario)
            ->with('exito', 'Usuario actualizado correctamente.');
    }

    public function desactivar(Usuario $usuario): RedirectResponse
    {
        Gate::authorize('desactivar', $usuario);

        $pendientes = $this->usuarioService->desactivar($usuario);

        $respuesta = redirect()
            ->route('usuarios.show', $usuario)
            ->with('exito', 'Usuario desactivado. Ya no puede iniciar sesión.');

        if ($pendientes > 0) {
            $respuesta->with('advertencia', "Este odontólogo tiene {$pendientes} cita(s) programada(s). Reasígnalas o cancélalas desde la agenda.");
        }

        return $respuesta;
    }

    public function activar(Usuario $usuario): RedirectResponse
    {
        Gate::authorize('activar', $usuario);

        $this->usuarioService->activar($usuario);

        return redirect()
            ->route('usuarios.show', $usuario)
            ->with('exito', 'Usuario reactivado. Ya puede iniciar sesión de nuevo.');
    }
}
