<?php

namespace App\Services;

use App\Exceptions\HorarioOcupadoException;
use App\Models\Cita;
use App\Models\Consultorio;
use App\Models\Odontologo;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * Regla de negocio: una cita no puede empalmarse con otra PROGRAMADA
 * del mismo odontólogo ni del mismo consultorio.
 *
 * La comprobación y el guardado ocurren dentro de una transacción que
 * bloquea las filas del odontólogo y del consultorio (SELECT ... FOR UPDATE).
 * Así, dos peticiones simultáneas para el mismo recurso se ejecutan una tras otra.
 *
 * Orden de bloqueo fijo (primero odontólogo, luego consultorio) para evitar deadlocks.
 */
class CitaService
{
    public function crear(array $datos): Cita
    {
        return DB::transaction(function () use ($datos) {
            $idOdontologo = (int) $datos['id_odontologo'];
            $idConsultorio = (int) $datos['id_consultorio'];
            $inicio = Carbon::parse($datos['inicio']);
            $fin = Carbon::parse($datos['fin']);

            $this->validarIntervalo($inicio, $fin);
            $this->bloquearRecursos($idOdontologo, $idConsultorio);
            $this->asegurarDisponibilidad($idOdontologo, $idConsultorio, $inicio, $fin);

            return Cita::create([
                'id_paciente' => $datos['id_paciente'],
                'id_odontologo' => $idOdontologo,
                'id_consultorio' => $idConsultorio,
                'inicio' => $inicio,
                'fin' => $fin,
                'estado' => Cita::PROGRAMADA,
                'motivo' => $datos['motivo'] ?? null,
            ]);
        });
    }

    public function actualizar(Cita $cita, array $datos): Cita
    {
        return DB::transaction(function () use ($cita, $datos) {
            $idOdontologo = (int) $datos['id_odontologo'];
            $idConsultorio = (int) $datos['id_consultorio'];
            $inicio = Carbon::parse($datos['inicio']);
            $fin = Carbon::parse($datos['fin']);

            $this->validarIntervalo($inicio, $fin);
            $this->bloquearRecursos($idOdontologo, $idConsultorio);
            $this->asegurarDisponibilidad($idOdontologo, $idConsultorio, $inicio, $fin, $cita->id_cita);

            $cita->update([
                'id_paciente' => $datos['id_paciente'],
                'id_odontologo' => $idOdontologo,
                'id_consultorio' => $idConsultorio,
                'inicio' => $inicio,
                'fin' => $fin,
                'motivo' => $datos['motivo'] ?? null,
            ]);

            return $cita;
        });
    }

    // La cancelación solo cambia el estado; el registro se conserva
    public function cancelar(Cita $cita): Cita
    {
        $cita->update(['estado' => Cita::CANCELADA]);

        return $cita;
    }

    private function validarIntervalo(Carbon $inicio, Carbon $fin): void
    {
        if ($fin->lessThanOrEqualTo($inicio)) {
            throw new InvalidArgumentException('La hora de fin debe ser posterior a la de inicio.');
        }
    }

    private function bloquearRecursos(int $idOdontologo, int $idConsultorio): void
    {
        Odontologo::whereKey($idOdontologo)->lockForUpdate()->firstOrFail();
        Consultorio::whereKey($idConsultorio)->lockForUpdate()->firstOrFail();
    }

    private function asegurarDisponibilidad(
        int $idOdontologo,
        int $idConsultorio,
        Carbon $inicio,
        Carbon $fin,
        ?int $excluirIdCita = null,
    ): void {
        $hayChoque = Cita::query()
            ->where('estado', Cita::PROGRAMADA)
            ->where('inicio', '<', $fin)
            ->where('fin', '>', $inicio)
            ->when($excluirIdCita, fn ($q) => $q->where('id_cita', '!=', $excluirIdCita))
            ->where(function ($q) use ($idOdontologo, $idConsultorio) {
                $q->where('id_odontologo', $idOdontologo)
                    ->orWhere('id_consultorio', $idConsultorio);
            })
            ->exists();

        if ($hayChoque) {
            throw new HorarioOcupadoException(
                'El odontólogo o el consultorio ya tienen una cita en ese horario. Selecciona otra fecha u hora.'
            );
        }
    }
}
