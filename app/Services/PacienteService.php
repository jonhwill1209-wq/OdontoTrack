<?php

namespace App\Services;

use App\Models\Paciente;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class PacienteService
{
    public function listar(
        string $buscar = '',
        string $estado = '1'
    ): LengthAwarePaginator {
        $consulta = Paciente::query();
        $buscar = trim($buscar);

        if ($buscar !== '') {
            $consulta->where(function (Builder $query) use ($buscar) {
                $query->where('nombres', 'like', "%{$buscar}%")
                    ->orWhere('apellidos', 'like', "%{$buscar}%")
                    ->orWhere('telefono', 'like', "%{$buscar}%");
            });
        }

        if (in_array($estado, ['0', '1'], true)) {
            $consulta->where('activo', $estado === '1');
        }

        return $consulta
            ->orderBy('apellidos')
            ->orderBy('nombres')
            ->orderBy('id_paciente')
            ->paginate(10)
            ->withQueryString();
    }

    public function registrar(array $datos): Paciente
    {
        $datos = $this->datosPermitidos($datos);
        $datos['activo'] = true;

        return Paciente::create($datos);
    }

    public function actualizar(Paciente $paciente, array $datos): Paciente
    {
        $paciente->update($this->datosPermitidos($datos));

        return $paciente->refresh();
    }

    public function desactivar(Paciente $paciente): void
    {
        $paciente->update(['activo' => false]);
    }

    private function datosPermitidos(array $datos): array
    {
        return Arr::only($datos, [
            'nombres',
            'apellidos',
            'fecha_nacimiento',
            'telefono',
            'correo',
        ]);
    }
}
