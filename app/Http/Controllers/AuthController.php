<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;

class AuthController extends Controller
{
    private const MAX_INTENTOS = 5;
    private const SEGUNDOS_BLOQUEO = 60;

    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $datos = $request->validated();
        $llave = $datos['correo'] . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($llave, self::MAX_INTENTOS)) {
            $segundos = RateLimiter::availableIn($llave);

            Log::warning('Inicio de sesión bloqueado por intentos fallidos.', [
                'correo' => $datos['correo'],
                'ip' => $request->ip(),
            ]);

            return back()
                ->withErrors(['correo' => "Demasiados intentos fallidos. Intenta de nuevo en {$segundos} segundos."])
                ->onlyInput('correo');
        }

        $cuentaInactiva = false;

        //El callback solo se ejecuta cuando la contraseña es correcta
        $valido = Auth::attemptWhen(
            ['correo' => $datos['correo'], 'password' => $datos['password']],
            function (Usuario $usuario) use (&$cuentaInactiva) {
                $cuentaInactiva = ! $usuario->activo;

                return $usuario->activo;
            }
        );

        if ($valido) {
            RateLimiter::clear($llave);
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        }

        if ($cuentaInactiva) {
            return back()
                ->withErrors(['correo' => 'Tu cuenta está desactivada. Comunícate con administración.'])
                ->onlyInput('correo');
        }

        RateLimiter::hit($llave, self::SEGUNDOS_BLOQUEO);

        $respuesta = back()
            ->withErrors(['correo' => 'Credenciales inválidas.'])
            ->onlyInput('correo');

        $aviso = $this->avisoIntentos(RateLimiter::remaining($llave, self::MAX_INTENTOS));

        return $aviso ? $respuesta->with('advertencia', $aviso) : $respuesta;
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('exito', 'Sesión cerrada correctamente.');
    }

    private function avisoIntentos(int $restantes): ?string
    {
        return match (true) {
            $restantes === 0 => 'Alcanzaste el límite de intentos. Espera ' . self::SEGUNDOS_BLOQUEO . ' segundos antes de volver a intentarlo.',
            $restantes === 1 => 'Te queda 1 intento antes de un bloqueo temporal.',
            $restantes === 2 => 'Te quedan 2 intentos antes de un bloqueo temporal.',
            default => null,
        };
    }
}
