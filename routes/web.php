<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Middleware\SinCache;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware(['guest', SinCache::class])->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
});


Route::middleware(['auth', SinCache::class])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    foreach (glob(__DIR__ . '/modules/*.php') as $file) {
        require $file;
    }
});

//Las direcciones inexistentes muestran el 404 propio con la sesión cargada
Route::fallback(fn () => abort(404));
