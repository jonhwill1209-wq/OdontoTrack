<?php

namespace App\Services;

use App\Enums\Rol;
use App\Models\Cita;
use App\Models\Odontologo;
use App\Support\Busqueda;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class OdontologoService
{
    public function __construct(
        private readonly UsuarioService $usuarioService
    ) {
    }

    public function listar(array $filtros): LengthAwarePaginator
    {
        $buscar = trim($filtros['buscar'] ?? '');
        $estado = $filtros['estado'] ?? '1';

        return Odontologo::query()
            ->select('odontologo.*')
            ->join('usuario', 'usuario.id_usuario', '=', 'odontologo.id_usuario')
            ->with('usuario')
            ->withCount('citasPendientes')
            ->when($buscar !== '', function (Builder $consulta) use ($buscar) {
                $patron = Busqueda::contiene($buscar);

                $consulta->where(function (Builder $q) use ($patron) {
                    $q->where('usuario.nombre', 'like', $patron)
                        ->orWhere('usuario.correo', 'like', $patron)
                        ->orWhere('odontologo.numero_colegiado', 'like', $patron);
                });
            })
            ->when(in_array($estado, ['0', '1'], true), fn (Builder $q) => $q->where('usuario.activo', $estado === '1'))
            ->orderBy('usuario.nombre')
            ->orderBy('odontologo.id_odontologo')
            ->paginate(10)
            ->withQueryString();
    }

    public function totales(): array
    {
        return [
            'total' => Odontologo::count(),
            'activos' => Odontologo::activos()->count(),
        ];
    }

    public function crear(array $datos): Odontologo
    {
        $usuario = $this->usuarioService->crear([...$datos, 'rol' => Rol::Odontologo->value]);

        return $usuario->odontologo;
    }

    public function actualizar(Odontologo $odontologo, array $datos): Odontologo
    {
        DB::transaction(function () use ($odontologo, $datos) {
            $this->usuarioService->actualizar($odontologo->usuario, $datos);
            $odontologo->update(['numero_colegiado' => $datos['numero_colegiado'] ?? null]);
        });

        return $odontologo->refresh();
    }

    public function proximasCitas(Odontologo $odontologo, int $limite = 8): Collection
    {
        return $odontologo->citasPendientes()
            ->with(['paciente', 'consultorio'])
            ->orderBy('inicio')
            ->limit($limite)
            ->get();
    }

    public function resumenCitas(Odontologo $odontologo): array
    {
        $porEstado = $odontologo->citas()
            ->selectRaw('estado, count(*) as total')
            ->groupBy('estado')
            ->pluck('total', 'estado');

        return [
            'pendientes' => $odontologo->citasPendientes()->count(),
            'realizadas' => (int) ($porEstado[Cita::REALIZADA] ?? 0),
            'canceladas' => (int) ($porEstado[Cita::CANCELADA] ?? 0),
        ];
    }
}
