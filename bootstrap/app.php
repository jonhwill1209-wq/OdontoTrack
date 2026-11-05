<?php

use App\Http\Middleware\VerificarSesion;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'rol' => \App\Http\Middleware\CheckRole::class,
        ]);

        $middleware->web(append: [
            VerificarSesion::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //Las contraseñas no se guardan en la sesión cuando un formulario regresa con errores
        $exceptions->dontFlash(['clave', 'clave_confirmation', 'clave_actual']);

        //Si el formulario de acceso o el de salida expiró se vuelve al login con un aviso en lugar de la página 419
        $exceptions->render(function (HttpException $e, Request $request) {
            if ($e->getStatusCode() !== 419 || ! $request->routeIs('login.attempt', 'logout')) {
                return null;
            }

            $mensaje = $request->routeIs('logout')
                ? 'Tu sesión ya había expirado.'
                : 'La página estuvo inactiva demasiado tiempo. Vuelve a intentarlo.';

            return redirect()->route('login')->with('advertencia', $mensaje);
        });
    })->create();
