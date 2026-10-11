<?php

namespace Tests\Feature;

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class BaseDatosTest extends TestCase
{
    use RefreshDatabase;

    public function test_las_diez_tablas_principales_existen(): void
    {
        $tablas = [
            'usuario',
            'odontologo',
            'consultorio',
            'paciente',
            'cita',
            'expediente',
            'anamnesis',
            'tratamiento',
            'evolucion_clinica',
            'registro_odontograma',
        ];

        foreach ($tablas as $tabla) {
            $this->assertTrue(
                Schema::hasTable($tabla),
                "No existe la tabla: {$tabla}"
            );
        }
    }

    public function test_no_se_puede_repetir_el_correo_de_un_usuario(): void
    {
        $usuario = [
            'nombre' => 'Usuario de prueba',
            'correo' => 'prueba@odontotrack.test',
            'clave_hash' => Hash::make('Odonto2026!'),
            'rol' => 'RECEPCIONISTA',
            'activo' => true,
        ];

        DB::table('usuario')->insert($usuario);

        $this->expectException(QueryException::class);

        DB::table('usuario')->insert([
            ...$usuario,
            'nombre' => 'Otro usuario',
        ]);
    }

    public function test_no_se_puede_crear_una_cita_con_paciente_inexistente(): void
    {
        $idUsuario = DB::table('usuario')->insertGetId([
            'nombre' => 'Doctora de prueba',
            'correo' => 'doctora@odontotrack.test',
            'clave_hash' => Hash::make('Odonto2026!'),
            'rol' => 'ODONTOLOGO',
            'activo' => true,
        ]);

        $idOdontologo = DB::table('odontologo')->insertGetId([
            'id_usuario' => $idUsuario,
            'numero_colegiado' => 'COL-001',
        ]);

        $idConsultorio = DB::table('consultorio')->insertGetId([
            'nombre' => 'Consultorio de prueba',
            'activo' => true,
        ]);

        $this->expectException(QueryException::class);

        DB::table('cita')->insert([
            'id_paciente' => 999999,
            'id_odontologo' => $idOdontologo,
            'id_consultorio' => $idConsultorio,
            'inicio' => '2026-10-10 08:00:00',
            'fin' => '2026-10-10 09:00:00',
            'estado' => 'PROGRAMADA',
            'motivo' => 'Prueba de clave foránea',
        ]);
    }
}
