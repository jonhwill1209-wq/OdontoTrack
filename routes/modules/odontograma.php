<?php

use App\Http\Controllers\OdontogramaController;
use Illuminate\Support\Facades\Route;

Route::middleware('rol:ODONTOLOGO')->group(function () {
    Route::get(
        '/pacientes/{paciente}/odontograma',
        [OdontogramaController::class, 'show']
    )
        ->whereNumber('paciente')
        ->name('odontograma.show');

    Route::post(
        '/pacientes/{paciente}/odontograma',
        [OdontogramaController::class, 'store']
    )
        ->whereNumber('paciente')
        ->name('odontograma.store');
});
