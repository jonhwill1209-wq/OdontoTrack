<?php

namespace App\Services;

use App\Exceptions\OperacionNoPermitidaException;
use App\Models\Consultorio;
use App\Support\Busqueda;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ConsultorioService
{
    public function listar(array $filtros): LengthAwarePaginator
    {
        $buscar = trim($filtros['buscar'] ?? '');
        $estado = $filtros['estado'] ?? '1';

        return Consultorio::query()
            ->withCount(['citas', 'citasPendientes'])
            ->when($buscar !== '', fn (Builder $q) => $q->where('nombre', 'like', Busqueda::contiene($buscar)))
            ->when(in_array($estado, ['0', '1'], true), fn (Builder $q) => $q->where('activo', $estado === '1'))
            ->orderBy('nombre')
            ->orderBy('id_consultorio')
            ->paginate(10)
            ->withQueryString();
    }

    public function totales(): array
    {
        return [
            'total' => Consultorio::count(),
            'activos' => Consultorio::activos()->count(),
        ];
    }

    public function crear(array $datos): Consultorio
    {
        return Consultorio::create([
            'nombre' => $datos['nombre'],
            'activo' => true,
        ]);
    }

    public function actualizar(Consultorio $consultorio, array $datos): Consultorio
    {
        $consultorio->update(['nombre' => $datos['nombre']]);

        return $consultorio->refresh();
    }

    //Devuelve cuántas citas futuras siguen asignadas a este consultorio
    public function desactivar(Consultorio $consultorio): int
    {
        $consultorio->update(['activo' => false]);

        return $consultorio->citasPendientes()->count();
    }

    public function activar(Consultorio $consultorio): void
    {
        $consultorio->update(['activo' => true]);
    }

    public function eliminar(Consultorio $consultorio): void
    {
        DB::transaction(function () use ($consultorio) {
            //Mismo bloqueo que usa CitaService, así nadie agenda en este consultorio mientras se elimina
            $bloqueado = Consultorio::whereKey($consultorio->id_consultorio)->lockForUpdate()->firstOrFail();

            if ($bloqueado->citas()->exists()) {
                throw new OperacionNoPermitidaException(
                    'No se puede eliminar porque tiene citas registradas. Desactívalo para que no se use en nuevas citas.'
                );
            }

            $bloqueado->delete();
        });
    }

    public function proximasCitas(Consultorio $consultorio, int $limite = 8): Collection
    {
        return $consultorio->citasPendientes()
            ->with(['paciente', 'odontologo.usuario'])
            ->orderBy('inicio')
            ->limit($limite)
            ->get();
    }
}
