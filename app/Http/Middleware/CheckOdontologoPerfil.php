<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckOdontologoPerfil
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless(
            $request->user()?->esOdontologo() && $request->user()->odontologo !== null,
            403,
            'Necesitas un perfil de odontólogo para acceder a esta sección.'
        );

        return $next($request);
    }
}
