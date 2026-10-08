<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
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

        if (Auth::attempt([
            'correo' => $datos['correo'],
            'password' => $datos['password'],
            'activo' => true,
        ])) {
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        }

        return back()
            ->withErrors(['correo' => 'Credenciales inválidas.'])
            ->onlyInput('correo');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
