<?php

// Responsable: Persona 5 (Agenda de citas)
// Ambos roles pueden consultar, crear, modificar y cancelar citas.

use App\Http\Controllers\CitaController;
use Illuminate\Support\Facades\Route;

Route::middleware('rol:RECEPCIONISTA,ODONTOLOGO')->group(function () {
    Route::get('/citas', [CitaController::class, 'index'])->name('citas.index');

    // IMPORTANTE: /citas/eventos y /citas/crear van ANTES de las rutas con {cita}
    Route::get('/citas/eventos', [CitaController::class, 'eventos'])->name('citas.eventos');
    Route::get('/citas/crear', [CitaController::class, 'create'])->name('citas.create');
    Route::post('/citas', [CitaController::class, 'store'])->name('citas.store');

    Route::get('/citas/{cita}/editar', [CitaController::class, 'edit'])
        ->whereNumber('cita')->name('citas.edit');
    Route::put('/citas/{cita}', [CitaController::class, 'update'])
        ->whereNumber('cita')->name('citas.update');
    Route::patch('/citas/{cita}/cancelar', [CitaController::class, 'cancelar'])
        ->whereNumber('cita')->name('citas.cancelar');
});
