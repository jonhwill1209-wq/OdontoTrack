<?php

namespace App\Http\Middleware;

use App\Enums\Rol;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $usuario = $request->user();

        if (! $usuario) {
            throw new AuthenticationException;
        }

        //Un rol mal escrito en una ruta cerraría la sección para todos sin dar aviso
        foreach ($roles as $rol) {
            if (Rol::tryFrom($rol) === null) {
                throw new InvalidArgumentException("El rol [{$rol}] usado en la ruta no existe.");
            }
        }

        if (! in_array($usuario->rol, $roles, true)) {
            Log::warning('Acceso denegado por rol.', [
                'usuario' => $usuario->id_usuario,
                'rol' => $usuario->rol,
                'ruta' => $request->path(),
            ]);

            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        return $next($request);
    }
}
