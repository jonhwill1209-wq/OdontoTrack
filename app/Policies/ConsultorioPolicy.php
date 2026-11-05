<?php

namespace App\Policies;

use App\Models\Consultorio;
use App\Models\Usuario;
use Illuminate\Auth\Access\Response;

class ConsultorioPolicy
{
    public function viewAny(Usuario $actual): Response
    {
        return $this->soloAdministracion($actual);
    }

    public function view(Usuario $actual, Consultorio $consultorio): Response
    {
        return $this->soloAdministracion($actual);
    }

    public function create(Usuario $actual): Response
    {
        return $this->soloAdministracion($actual);
    }

    public function update(Usuario $actual, Consultorio $consultorio): Response
    {
        return $this->soloAdministracion($actual);
    }

    public function desactivar(Usuario $actual, Consultorio $consultorio): Response
    {
        return $this->soloAdministracion($actual);
    }

    public function activar(Usuario $actual, Consultorio $consultorio): Response
    {
        return $this->soloAdministracion($actual);
    }

    public function delete(Usuario $actual, Consultorio $consultorio): Response
    {
        return $this->soloAdministracion($actual);
    }

    private function soloAdministracion(Usuario $actual): Response
    {
        return $actual->esRecepcionista()
            ? Response::allow()
            : Response::deny('Solo recepción o administración puede gestionar los consultorios.');
    }
}
