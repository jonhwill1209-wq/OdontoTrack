<?php

namespace Tests\Feature;

use App\Models\Paciente;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Tests\TestCase;

class PacienteExportacionTest extends TestCase
{
    use DatabaseTransactions;

    private function usuario(string $rol = 'RECEPCIONISTA'): Usuario
    {
        return Usuario::create([
            'nombre' => 'Usuario de prueba',
            'correo' => Str::uuid() . '@prueba.test',
            'clave_hash' => Hash::make('Prueba2026!'),
            'rol' => $rol,
            'activo' => true,
        ]);
    }

    private function paciente(array $datos = []): Paciente
    {
        return Paciente::create(array_merge([
            'nombres' => 'José Ángel',
            'apellidos' => 'ExportacionControl',
            'fecha_nacimiento' => '2000-01-01',
            'telefono' => '7123-4567',
            'correo' => null,
            'activo' => true,
        ], $datos));
    }

    private function descargar(array $filtros = []): array
    {
        $response = $this->get(route('pacientes.exportar', $filtros));

        $response->assertOk();
        $response->assertDownload();

        $archivo = $response->baseResponse;

        $this->assertInstanceOf(BinaryFileResponse::class, $archivo);

        $ruta = $archivo->getFile()->getPathname();
        $libro = null;

        try {
            $libro = (new Xlsx())->load($ruta);
            $hoja = $libro->getActiveSheet();

            return [
                'filas' => $hoja->toArray(null, false, false, false),
                'tipo_telefono' => $hoja->getCell('E2')->getDataType(),
                'tipo_correo' => $hoja->getCell('F2')->getDataType(),
            ];
        } finally {
            $libro?->disconnectWorksheets();

            if (is_file($ruta)) {
                unlink($ruta);
            }
        }
    }

    public function test_sin_sesion_no_puede_exportar(): void
    {
        $this->get(route('pacientes.exportar'))
            ->assertRedirect(route('login'));
    }

    public function test_odontologo_no_puede_exportar_ni_ve_el_boton(): void
    {
        $this->actingAs($this->usuario('ODONTOLOGO'));

        $this->get(route('pacientes.exportar'))
            ->assertForbidden();

        $this->get(route('pacientes.index'))
            ->assertOk()
            ->assertDontSee('Exportar a Excel');
    }

    public function test_recepcion_exporta_todos_los_activos_sin_paginar(): void
    {
        $this->actingAs($this->usuario());

        $ids = [];

        for ($i = 0; $i < 12; $i++) {
            $ids[] = $this->paciente()->id_paciente;
        }

        $this->paciente(['activo' => false]);

        $resultado = $this->descargar([
            'buscar' => 'ExportacionControl',
            'page' => 2,
        ]);

        $filas = $resultado['filas'];

        $this->assertSame([
            'ID',
            'Nombres',
            'Apellidos',
            'Fecha de nacimiento',
            'Teléfono',
            'Correo electrónico',
            'Estado',
        ], $filas[0]);

        $this->assertCount(13, $filas);

        $exportados = array_slice($filas, 1);

        $this->assertSame($ids, array_column($exportados, 0));

        foreach ($exportados as $fila) {
            $this->assertSame('Activo', $fila[6]);
        }

        $this->get(route('pacientes.index'))
            ->assertOk()
            ->assertSee('Exportar a Excel');
    }

    public function test_exportacion_respeta_busqueda_y_estado_juntos(): void
    {
        $this->actingAs($this->usuario());

        $esperado = $this->paciente([
            'nombres' => 'NombreExportacionUnico',
            'apellidos' => 'ApellidoExportacionUnico',
            'telefono' => '7986-1352',
        ]);

        $this->paciente([
            'nombres' => 'NombreExportacionUnico',
            'apellidos' => 'ApellidoExportacionUnico',
            'telefono' => '7986-1352',
            'activo' => false,
        ]);

        $this->paciente();

        foreach ([
                     'NombreExportacionUnico',
                     'ApellidoExportacionUnico',
                     '7986-1352',
                 ] as $buscar) {
            $resultado = $this->descargar([
                'buscar' => $buscar,
                'estado' => '1',
            ]);

            $this->assertCount(2, $resultado['filas']);

            $this->assertSame(
                $esperado->id_paciente,
                $resultado['filas'][1][0]
            );
        }
    }

    public function test_exporta_inactivos_o_todos_segun_el_filtro(): void
    {
        $this->actingAs($this->usuario());

        $activo = $this->paciente();
        $inactivo = $this->paciente(['activo' => false]);

        $resultado = $this->descargar([
            'buscar' => 'ExportacionControl',
            'estado' => '0',
        ]);

        $this->assertCount(2, $resultado['filas']);
        $this->assertSame($inactivo->id_paciente, $resultado['filas'][1][0]);
        $this->assertSame('Inactivo', $resultado['filas'][1][6]);

        $resultado = $this->descargar([
            'buscar' => 'ExportacionControl',
            'estado' => 'todos',
        ]);

        $this->assertCount(3, $resultado['filas']);

        $this->assertSame(
            [$activo->id_paciente, $inactivo->id_paciente],
            array_column(array_slice($resultado['filas'], 1), 0)
        );
    }

    public function test_exportacion_rechaza_filtros_invalidos(): void
    {
        $this->actingAs($this->usuario());

        foreach ([
                     ['buscar' => str_repeat('a', 101)],
                     ['buscar' => ['valor']],
                     ['estado' => 'incorrecto'],
                 ] as $filtros) {
            $this->getJson(route('pacientes.exportar', $filtros))
                ->assertUnprocessable()
                ->assertJsonValidationErrors(array_keys($filtros));
        }
    }

    public function test_excel_conserva_textos_ceros_y_campos_vacios(): void
    {
        $this->actingAs($this->usuario());

        $paciente = $this->paciente([
            'telefono' => '01234567',
            'correo' => '=1+1',
            'fecha_nacimiento' => null,
        ]);

        $resultado = $this->descargar([
            'buscar' => 'ExportacionControl',
        ]);

        $fila = $resultado['filas'][1];

        $this->assertSame('José Ángel', $fila[1]);
        $this->assertSame('01234567', $fila[4]);
        $this->assertSame('=1+1', $fila[5]);
        $this->assertSame(DataType::TYPE_STRING, $resultado['tipo_telefono']);
        $this->assertSame(DataType::TYPE_STRING, $resultado['tipo_correo']);
        $this->assertTrue($fila[3] === null || $fila[3] === '');

        $paciente->update([
            'telefono' => null,
            'correo' => null,
        ]);

        $resultado = $this->descargar([
            'buscar' => 'ExportacionControl',
        ]);

        $fila = $resultado['filas'][1];

        $this->assertTrue($fila[4] === null || $fila[4] === '');
        $this->assertTrue($fila[5] === null || $fila[5] === '');
    }
}
