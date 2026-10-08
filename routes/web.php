<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

// TEMPORAL (solo entorno local): entrar sin el login real mientras Persona 3 lo termina.
// Ejemplo: http://127.0.0.1:8000/dev-login/doctor1@odontotrack.test
// Persona 3 elimina este bloque cuando el login funcione.
if (app()->environment('local')) {
    Route::get('/dev-login/{correo}', function (string $correo) {
        Auth::login(Usuario::where('correo', $correo)->firstOrFail());

        return redirect('/dashboard');
    });
}

// Solo para visitantes sin sesión
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
});

// Todo lo demás exige sesión iniciada
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Cada módulo tiene su propio archivo de rutas y un solo responsable.
    // NO agregues rutas de otro módulo aquí.
    require __DIR__ . '/modules/pacientes.php';
    require __DIR__ . '/modules/citas.php';
    require __DIR__ . '/modules/expedientes.php';
    require __DIR__ . '/modules/odontograma.php';
});
