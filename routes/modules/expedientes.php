<?php

use App\Http\Controllers\ExpedienteController;
use App\Http\Controllers\TratamientoController;
use App\Http\Middleware\CheckOdontologoPerfil;
use Illuminate\Support\Facades\Route;

Route::middleware(['rol:ODONTOLOGO', CheckOdontologoPerfil::class])->group(function () {
    Route::get('/expedientes', [ExpedienteController::class, 'index'])->name('expedientes.index');
    Route::get('/expedientes/{paciente}', [ExpedienteController::class, 'show'])
        ->whereNumber('paciente')->name('expedientes.show');
    Route::put('/expedientes/{expediente}/anamnesis', [ExpedienteController::class, 'actualizarAnamnesis'])
        ->whereNumber('expediente')->name('expedientes.anamnesis.update');
    Route::post('/expedientes/{expediente}/archivos', [ExpedienteController::class, 'subirArchivo'])
        ->whereNumber('expediente')->name('expedientes.archivos.store');
    Route::get('/expediente-archivos/{archivo}', [ExpedienteController::class, 'descargarArchivo'])
        ->whereNumber('archivo')->name('expedientes.archivos.download');
    Route::delete('/expediente-archivos/{archivo}', [ExpedienteController::class, 'eliminarArchivo'])
        ->whereNumber('archivo')->name('expedientes.archivos.destroy');

    Route::get('/expedientes/{expediente}/tratamientos/crear', [TratamientoController::class, 'create'])
        ->whereNumber('expediente')->name('tratamientos.create');
    Route::post('/expedientes/{expediente}/tratamientos', [TratamientoController::class, 'store'])
        ->whereNumber('expediente')->name('tratamientos.store');
    Route::get('/tratamientos/{tratamiento}', [TratamientoController::class, 'show'])
        ->whereNumber('tratamiento')->name('tratamientos.show');
    Route::get('/tratamientos/{tratamiento}/editar', [TratamientoController::class, 'edit'])
        ->whereNumber('tratamiento')->name('tratamientos.edit');
    Route::put('/tratamientos/{tratamiento}', [TratamientoController::class, 'update'])
        ->whereNumber('tratamiento')->name('tratamientos.update');
    Route::delete('/tratamientos/{tratamiento}', [TratamientoController::class, 'destroy'])
        ->whereNumber('tratamiento')->name('tratamientos.destroy');
    Route::post('/tratamientos/{tratamiento}/evoluciones', [TratamientoController::class, 'registrarEvolucion'])
        ->whereNumber('tratamiento')->name('tratamientos.evoluciones.store');
});
