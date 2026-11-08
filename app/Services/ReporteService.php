<?php

namespace App\Services;

use App\Models\Cita;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class ReporteService
{
    public function citas(string $desde, string $hasta, ?int $idOdontologo = null): Collection
    {
        return $this->consultaCitas($desde, $hasta, $idOdontologo)
            ->with(['paciente', 'odontologo.usuario', 'consultorio'])
            ->orderBy('inicio')
            ->get();
    }

    public function totales(string $desde, string $hasta, ?int $idOdontologo = null): array
    {
        $conteos = $this->consultaCitas($desde, $hasta, $idOdontologo)
            ->selectRaw('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->pluck('total', 'estado');

        return [
            'total' => (int) $conteos->sum(),
            'programadas' => (int) $conteos->get(Cita::PROGRAMADA, 0),
            'realizadas' => (int) $conteos->get(Cita::REALIZADA, 0),
            'canceladas' => (int) $conteos->get(Cita::CANCELADA, 0),
        ];
    }

    public function porDia(string $desde, string $hasta, ?int $idOdontologo = null): Collection
    {
        return $this->consultaCitas($desde, $hasta, $idOdontologo)
            ->selectRaw('DATE(inicio) as fecha, COUNT(*) as total')
            ->groupByRaw('DATE(inicio)')
            ->orderBy('fecha')
            ->get();
    }

    private function consultaCitas(string $desde, string $hasta, ?int $idOdontologo): Builder
    {
        return Cita::query()
            ->whereDate('inicio', '>=', $desde)
            ->whereDate('inicio', '<=', $hasta)
            ->when($idOdontologo !== null, fn (Builder $query) => $query->where('id_odontologo', $idOdontologo));
    }
}
