<?php

use App\Http\Controllers\PacienteController;
use Illuminate\Support\Facades\Route;

// Consultar pacientes: recepción y odontólogos.
Route::middleware('rol:RECEPCIONISTA,ODONTOLOGO')->group(function () {
    Route::get('/pacientes', [PacienteController::class, 'index'])
        ->name('pacientes.index');

    // Registrar, editar y desactivar: solo recepción.
    Route::middleware('rol:RECEPCIONISTA')->group(function () {
        Route::get('/pacientes/crear', [PacienteController::class, 'create'])
            ->name('pacientes.create');

        Route::post('/pacientes', [PacienteController::class, 'store'])
            ->name('pacientes.store');

        Route::get('/pacientes/{paciente}/editar', [PacienteController::class, 'edit'])
            ->whereNumber('paciente')
            ->name('pacientes.edit');

        Route::put('/pacientes/{paciente}', [PacienteController::class, 'update'])
            ->whereNumber('paciente')
            ->name('pacientes.update');

        Route::delete('/pacientes/{paciente}', [PacienteController::class, 'destroy'])
            ->whereNumber('paciente')
            ->name('pacientes.destroy');
    });

    Route::get('/pacientes/{paciente}', [PacienteController::class, 'show'])
        ->whereNumber('paciente')
        ->name('pacientes.show');
});
