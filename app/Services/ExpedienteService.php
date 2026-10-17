<?php

namespace App\Services;

use App\Models\Expediente;
use App\Models\EvolucionClinica;
use App\Models\Paciente;
use App\Models\Tratamiento;

class ExpedienteService
{
    public function obtenerOCrearPorPaciente(
        Paciente $paciente
    ): Expediente {
        return Expediente::firstOrCreate([
            'id_paciente' => $paciente->id_paciente,
        ]);
    }

    public function actualizarAnamnesis(Expediente $expediente, array $datos): void
    {
        $expediente->anamnesis()->updateOrCreate(
            ['id_expediente' => $expediente->id_expediente],
            $datos
        );
    }

    public function crearTratamiento(Expediente $expediente, array $datos): Tratamiento
    {
        return $expediente->tratamientos()->create($datos);
    }

    public function actualizarTratamiento(Tratamiento $tratamiento, array $datos): void
    {
        $tratamiento->update($datos);
    }

    public function agregarEvolucion(
        Tratamiento $tratamiento,
        int $idOdontologo,
        string $observacion
    ): EvolucionClinica {
        return $tratamiento->evoluciones()->create([
            'id_odontologo' => $idOdontologo,
            'observacion' => $observacion,
        ]);
    }
}
