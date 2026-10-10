<?php

namespace App\Services;

use App\Models\Expediente;
use App\Models\Paciente;

class ExpedienteService
{
    public function obtenerOCrearPorPaciente(
        Paciente $paciente
    ): Expediente {
        return Expediente::firstOrCreate([
            'id_paciente' => $paciente->id_paciente,
        ]);
    }
}
