<?php

//Responsable: Persona 2 (Seguridad, usuarios y catálogos)

use App\Http\Controllers\ConsultorioController;
use App\Http\Controllers\CuentaController;
use App\Http\Controllers\OdontologoController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::middleware('rol:RECEPCIONISTA,ODONTOLOGO')->group(function () {
    Route::get('/mi-cuenta', [CuentaController::class, 'edit'])->name('cuenta.edit');
    Route::put('/mi-cuenta/clave', [CuentaController::class, 'actualizarClave'])->name('cuenta.clave');
});

Route::middleware('rol:RECEPCIONISTA')->group(function () {
    Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
    Route::get('/usuarios/crear', [UsuarioController::class, 'create'])->name('usuarios.create');
    Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
    Route::get('/usuarios/{usuario}', [UsuarioController::class, 'show'])->whereNumber('usuario')->name('usuarios.show');
    Route::get('/usuarios/{usuario}/editar', [UsuarioController::class, 'edit'])->whereNumber('usuario')->name('usuarios.edit');
    Route::put('/usuarios/{usuario}', [UsuarioController::class, 'update'])->whereNumber('usuario')->name('usuarios.update');
    Route::patch('/usuarios/{usuario}/desactivar', [UsuarioController::class, 'desactivar'])->whereNumber('usuario')->name('usuarios.desactivar');
    Route::patch('/usuarios/{usuario}/activar', [UsuarioController::class, 'activar'])->whereNumber('usuario')->name('usuarios.activar');

    Route::get('/odontologos', [OdontologoController::class, 'index'])->name('odontologos.index');
    Route::get('/odontologos/crear', [OdontologoController::class, 'create'])->name('odontologos.create');
    Route::post('/odontologos', [OdontologoController::class, 'store'])->name('odontologos.store');
    Route::get('/odontologos/{odontologo}', [OdontologoController::class, 'show'])->whereNumber('odontologo')->name('odontologos.show');
    Route::get('/odontologos/{odontologo}/editar', [OdontologoController::class, 'edit'])->whereNumber('odontologo')->name('odontologos.edit');
    Route::put('/odontologos/{odontologo}', [OdontologoController::class, 'update'])->whereNumber('odontologo')->name('odontologos.update');
    Route::patch('/odontologos/{odontologo}/desactivar', [OdontologoController::class, 'desactivar'])->whereNumber('odontologo')->name('odontologos.desactivar');
    Route::patch('/odontologos/{odontologo}/activar', [OdontologoController::class, 'activar'])->whereNumber('odontologo')->name('odontologos.activar');

    Route::get('/consultorios', [ConsultorioController::class, 'index'])->name('consultorios.index');
    Route::get('/consultorios/crear', [ConsultorioController::class, 'create'])->name('consultorios.create');
    Route::post('/consultorios', [ConsultorioController::class, 'store'])->name('consultorios.store');
    Route::get('/consultorios/{consultorio}', [ConsultorioController::class, 'show'])->whereNumber('consultorio')->name('consultorios.show');
    Route::get('/consultorios/{consultorio}/editar', [ConsultorioController::class, 'edit'])->whereNumber('consultorio')->name('consultorios.edit');
    Route::put('/consultorios/{consultorio}', [ConsultorioController::class, 'update'])->whereNumber('consultorio')->name('consultorios.update');
    Route::patch('/consultorios/{consultorio}/desactivar', [ConsultorioController::class, 'desactivar'])->whereNumber('consultorio')->name('consultorios.desactivar');
    Route::patch('/consultorios/{consultorio}/activar', [ConsultorioController::class, 'activar'])->whereNumber('consultorio')->name('consultorios.activar');
    Route::delete('/consultorios/{consultorio}', [ConsultorioController::class, 'destroy'])->whereNumber('consultorio')->name('consultorios.destroy');
});
