<?php

use App\Http\Controllers\ExpedienteController;
use Illuminate\Support\Facades\Route;

// Responsable: Persona 6 (Expediente clínico). Solo ODONTOLOGO.
Route::middleware('rol:ODONTOLOGO')->group(function () {
    Route::get('/pacientes/{paciente}/expediente', [ExpedienteController::class, 'show'])
        ->whereNumber('paciente')
        ->name('expedientes.show');

    Route::put('/pacientes/{paciente}/expediente/anamnesis', [ExpedienteController::class, 'actualizarAnamnesis'])
        ->whereNumber('paciente')
        ->name('expedientes.anamnesis.update');

    Route::post('/pacientes/{paciente}/expediente/tratamientos', [ExpedienteController::class, 'guardarTratamiento'])
        ->whereNumber('paciente')
        ->name('expedientes.tratamientos.store');

    Route::put('/pacientes/{paciente}/expediente/tratamientos/{tratamiento}', [ExpedienteController::class, 'actualizarTratamiento'])
        ->whereNumber('paciente')
        ->whereNumber('tratamiento')
        ->name('expedientes.tratamientos.update');

    Route::post('/pacientes/{paciente}/expediente/tratamientos/{tratamiento}/evoluciones', [ExpedienteController::class, 'agregarEvolucion'])
        ->whereNumber('paciente')
        ->whereNumber('tratamiento')
        ->name('expedientes.evoluciones.store');
});
