<?php

namespace App\Policies;

use App\Models\Usuario;
use Illuminate\Auth\Access\Response;

class UsuarioPolicy
{
    public function viewAny(Usuario $actual): Response
    {
        return $this->soloAdministracion($actual);
    }

    public function view(Usuario $actual, Usuario $usuario): Response
    {
        return $this->soloAdministracion($actual);
    }

    public function create(Usuario $actual): Response
    {
        return $this->soloAdministracion($actual);
    }

    public function update(Usuario $actual, Usuario $usuario): Response
    {
        return $this->soloAdministracion($actual);
    }

    public function desactivar(Usuario $actual, Usuario $usuario): Response
    {
        if ($actual->is($usuario)) {
            return Response::deny('No puedes desactivar tu propia cuenta.');
        }

        return $this->soloAdministracion($actual);
    }

    public function activar(Usuario $actual, Usuario $usuario): Response
    {
        return $this->soloAdministracion($actual);
    }

    private function soloAdministracion(Usuario $actual): Response
    {
        return $actual->esRecepcionista()
            ? Response::allow()
            : Response::deny('Solo recepción o administración puede gestionar las cuentas de usuario.');
    }
}
