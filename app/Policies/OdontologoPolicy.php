<?php

namespace App\Policies;

use App\Models\Odontologo;
use App\Models\Usuario;
use Illuminate\Auth\Access\Response;

class OdontologoPolicy
{
    public function viewAny(Usuario $actual): Response
    {
        return $this->soloAdministracion($actual);
    }

    public function view(Usuario $actual, Odontologo $odontologo): Response
    {
        return $this->soloAdministracion($actual);
    }

    public function create(Usuario $actual): Response
    {
        return $this->soloAdministracion($actual);
    }

    public function update(Usuario $actual, Odontologo $odontologo): Response
    {
        return $this->soloAdministracion($actual);
    }

    public function desactivar(Usuario $actual, Odontologo $odontologo): Response
    {
        return $this->soloAdministracion($actual);
    }

    public function activar(Usuario $actual, Odontologo $odontologo): Response
    {
        return $this->soloAdministracion($actual);
    }

    private function soloAdministracion(Usuario $actual): Response
    {
        return $actual->esRecepcionista()
            ? Response::allow()
            : Response::deny('Solo recepción o administración puede gestionar el catálogo de odontólogos.');
    }
}
