<?php

namespace App\Http\Middleware;

use App\Models\Usuario;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

//Termina la sesión si administración desactivó la cuenta o si su contraseña cambió desde otro lugar
class VerificarSesion
{
    public function handle(Request $request, Closure $next): Response
    {
        $usuario = $request->user();

        if ($usuario && $request->hasSession()) {
            if (! $usuario->activo) {
                return $this->cerrar($request, 'Tu cuenta fue desactivada. Comunícate con administración.');
            }

            $guardada = $request->session()->get($this->llave($usuario));

            if ($guardada !== null && ! hash_equals($guardada, $this->huella($usuario->getAuthPassword()))) {
                return $this->cerrar($request, 'Tu contraseña cambió. Inicia sesión con la nueva contraseña.');
            }
        }

        $respuesta = $next($request);

        //Se guarda al final y se relee tras un envío para que cambiar la propia contraseña no cierre esta sesión
        $usuario = $request->user();

        if ($usuario && $request->hasSession()) {
            $hash = $request->isMethodSafe()
                ? $usuario->getAuthPassword()
                : Usuario::whereKey($usuario->getAuthIdentifier())->value('clave_hash');

            $request->session()->put($this->llave($usuario), $this->huella((string) $hash));
        }

        return $respuesta;
    }

    private function llave(Usuario $usuario): string
    {
        return 'huella_clave_' . $usuario->getAuthIdentifier();
    }

    private function huella(string $hash): string
    {
        return hash_hmac('sha256', $hash, (string) config('app.key'));
    }

    private function cerrar(Request $request, string $mensaje): Response
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $request->expectsJson()
            ? response()->json(['message' => $mensaje], 401)
            : redirect()->route('login')->with('error', $mensaje);
    }
}
