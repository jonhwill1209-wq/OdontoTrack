<?php

use App\Http\Controllers\ArchivoClinicoController;
use App\Http\Controllers\ExpedienteController;
use Illuminate\Support\Facades\Route;

Route::middleware('rol:ODONTOLOGO')->prefix('pacientes/{paciente}/expediente')->name('expedientes.')->group(function () {
    Route::get('/', [ExpedienteController::class, 'show'])->name('show');
    Route::put('/anamnesis', [ExpedienteController::class, 'anamnesis'])->name('anamnesis');
    Route::post('/tratamientos', [ExpedienteController::class, 'tratamiento'])->name('tratamientos.store');
    Route::put('/tratamientos/{tratamiento}', [ExpedienteController::class, 'tratamiento'])->name('tratamientos.update');
    Route::post('/tratamientos/{tratamiento}/evoluciones', [ExpedienteController::class, 'evolucion'])->name('evoluciones.store');
    Route::put('/tratamientos/{tratamiento}/evoluciones/{evolucion}', [ExpedienteController::class, 'evolucion'])->name('evoluciones.update');
    Route::post('/archivos', [ArchivoClinicoController::class, 'store'])->name('archivos.store');
    Route::get('/archivos/{archivo}', [ArchivoClinicoController::class, 'show'])->name('archivos.show');
});
