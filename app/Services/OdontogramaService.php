<?php

namespace App\Services;

use App\Models\Expediente;
use App\Models\Odontologo;
use App\Models\Paciente;
use App\Models\RegistroOdontograma;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class OdontogramaService
{
    public function __construct(
        private readonly ExpedienteService $expedienteService
    ) {
    }

    public function listar(Expediente $expediente): Collection
    {
        return RegistroOdontograma::query()
            ->where('id_expediente', $expediente->id_expediente)
            ->with('odontologo.usuario')
            ->orderByDesc('registrado_en')
            ->orderByDesc('id_registro')
            ->get();
    }

    public function registrar(
        Paciente $paciente,
        Odontologo $odontologo,
        array $datos
    ): RegistroOdontograma {
        return DB::transaction(function () use (
            $paciente,
            $odontologo,
            $datos
        ) {
            $expediente = $this->expedienteService
                ->obtenerOCrearPorPaciente($paciente);

            return RegistroOdontograma::create([
                'id_expediente' => $expediente->id_expediente,
                'id_odontologo' => $odontologo->id_odontologo,
                'pieza' => $datos['pieza'],
                'cara' => $datos['cara'],
                'condicion' => $datos['condicion'],
            ]);
        });
    }
}
