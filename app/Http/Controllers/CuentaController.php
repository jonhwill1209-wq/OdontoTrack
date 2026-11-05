<?php

namespace App\Http\Controllers;

use App\Http\Requests\CambiarClaveRequest;
use App\Services\UsuarioService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

//Datos de la cuenta propia y cambio de contraseña, para cualquier rol
class CuentaController extends Controller
{
    public function __construct(
        private readonly UsuarioService $usuarioService
    ) {
    }

    public function edit(Request $request): View
    {
        return view('auth.cuenta', [
            'usuario' => $request->user()->load('odontologo'),
        ]);
    }

    public function actualizarClave(CambiarClaveRequest $request): RedirectResponse
    {
        $this->usuarioService->cambiarClave($request->user(), $request->validated('clave'));

        return redirect()
            ->route('cuenta.edit')
            ->with('exito', 'Contraseña actualizada. Si tenías la cuenta abierta en otro equipo, esa sesión se cerrará.');
    }
}
