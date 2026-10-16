<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    private const MAX_INTENTOS = 5;

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $datos = $request->validate([
            'correo' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'correo.required' => 'Ingresa tu correo.',
            'correo.email' => 'El correo no tiene un formato válido.',
            'password.required' => 'Ingresa tu contraseña.',
        ]);

        $llave = Str::lower($datos['correo']) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($llave, self::MAX_INTENTOS)) {
            $segundos = RateLimiter::availableIn($llave);

            return back()
                ->withErrors(['correo' => "Demasiados intentos fallidos. Intenta de nuevo en {$segundos} segundos."])
                ->onlyInput('correo');
        }

        if (Auth::attempt([
            'correo' => $datos['correo'],
            'password' => $datos['password'],
            'activo' => true,
        ])) {
            RateLimiter::clear($llave);
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        }

        RateLimiter::hit($llave, 60);

        return back()
            ->withErrors(['correo' => 'Credenciales inválidas.'])
            ->onlyInput('correo');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('exito', 'Sesión cerrada correctamente.');
    }
}
