<?php

namespace App\Services;

use App\Models\Odontologo;
use App\Models\Usuario;
use App\Support\Busqueda;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuarioService
{
    public function listar(array $filtros): LengthAwarePaginator
    {
        $buscar = trim($filtros['buscar'] ?? '');
        $rol = $filtros['rol'] ?? '';
        $estado = $filtros['estado'] ?? '1';

        return Usuario::query()
            ->when($buscar !== '', function (Builder $consulta) use ($buscar) {
                $patron = Busqueda::contiene($buscar);

                $consulta->where(function (Builder $q) use ($patron) {
                    $q->where('nombre', 'like', $patron)
                        ->orWhere('correo', 'like', $patron);
                });
            })
            ->when($rol !== '', fn (Builder $q) => $q->where('rol', $rol))
            ->when(in_array($estado, ['0', '1'], true), fn (Builder $q) => $q->where('activo', $estado === '1'))
            ->orderBy('nombre')
            ->orderBy('id_usuario')
            ->paginate(10)
            ->withQueryString();
    }

    public function totales(): array
    {
        return [
            'total' => Usuario::count(),
            'activos' => Usuario::activos()->count(),
        ];
    }

    public function crear(array $datos): Usuario
    {
        return DB::transaction(function () use ($datos) {
            $usuario = Usuario::create([
                'nombre' => $datos['nombre'],
                'correo' => $datos['correo'],
                'clave_hash' => Hash::make($datos['clave']),
                'rol' => $datos['rol'],
                'activo' => true,
            ]);

            //Toda cuenta de odontólogo necesita su perfil profesional para agenda y expediente
            if ($usuario->esOdontologo()) {
                Odontologo::create([
                    'id_usuario' => $usuario->id_usuario,
                    'numero_colegiado' => $datos['numero_colegiado'] ?? null,
                ]);
            }

            return $usuario;
        });
    }

    public function actualizar(Usuario $usuario, array $datos): Usuario
    {
        $cambios = [
            'nombre' => $datos['nombre'],
            'correo' => $datos['correo'],
        ];

        if (! empty($datos['clave'])) {
            $cambios['clave_hash'] = Hash::make($datos['clave']);
        }

        $usuario->update($cambios);

        return $usuario->refresh();
    }

    public function cambiarClave(Usuario $usuario, string $clave): void
    {
        $usuario->update(['clave_hash' => Hash::make($clave)]);
    }

    //Devuelve cuántas citas futuras quedan asignadas para advertir a recepción
    public function desactivar(Usuario $usuario): int
    {
        $usuario->update(['activo' => false]);

        return $usuario->odontologo?->citasPendientes()->count() ?? 0;
    }

    public function activar(Usuario $usuario): void
    {
        $usuario->update(['activo' => true]);
    }
}
