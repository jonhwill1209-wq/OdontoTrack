<?php

use App\Http\Controllers\ReporteController;
use Illuminate\Support\Facades\Route;

Route::middleware('rol:RECEPCIONISTA,ODONTOLOGO')->group(function () {
    Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
    Route::get('/reportes/citas.xlsx', [ReporteController::class, 'excel'])->name('reportes.excel');
    Route::get('/reportes/citas.pdf', [ReporteController::class, 'pdf'])->name('reportes.pdf');
});
