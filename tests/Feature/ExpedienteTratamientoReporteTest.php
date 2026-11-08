<?php

namespace Tests\Feature;

use App\Models\Cita;
use App\Models\Consultorio;
use App\Models\Expediente;
use App\Models\Odontologo;
use App\Models\Paciente;
use App\Models\Tratamiento;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class ExpedienteTratamientoReporteTest extends TestCase
{
    use RefreshDatabase;

    private function usuario(string $rol = 'ODONTOLOGO'): Usuario
    {
        return Usuario::create([
            'nombre' => 'Usuario de prueba',
            'correo' => Str::uuid().'@prueba.test',
            'clave_hash' => Hash::make('Clave2026!'),
            'rol' => $rol,
            'activo' => true,
        ]);
    }

    private function odontologo(): Usuario
    {
        $usuario = $this->usuario();
        Odontologo::create([
            'id_usuario' => $usuario->id_usuario,
            'numero_colegiado' => Str::upper(Str::random(10)),
        ]);

        return $usuario->load('odontologo');
    }

    private function paciente(): Paciente
    {
        return Paciente::create([
            'nombres' => 'Paciente',
            'apellidos' => 'Integracion',
            'activo' => true,
        ]);
    }

    public function test_odontologo_gestiona_anamnesis_archivos_y_su_acceso(): void
    {
        Storage::fake('local');
        $doctor = $this->odontologo();
        $paciente = $this->paciente();

        $this->actingAs($doctor)
            ->get(route('expedientes.show', $paciente))
            ->assertOk()
            ->assertSee('Anamnesis')
            ->assertSee('Documentos e imágenes');

        $expediente = Expediente::where('id_paciente', $paciente->id_paciente)->firstOrFail();
        $this->put(route('expedientes.anamnesis.update', $expediente), [
            'alergias' => 'Penicilina',
            'antecedentes' => 'Hipertensión controlada',
            'medicamentos' => 'Ninguno',
        ])->assertSessionHas('exito');

        $this->assertDatabaseHas('anamnesis', [
            'id_expediente' => $expediente->id_expediente,
            'alergias' => 'Penicilina',
        ]);

        $this->post(route('expedientes.archivos.store', $expediente), [
            'archivo' => UploadedFile::fake()->image('radiografia.png'),
        ])->assertSessionHas('exito');

        $archivo = $expediente->archivos()->firstOrFail();
        Storage::disk('local')->assertExists($archivo->ruta);
        $this->get(route('expedientes.archivos.download', $archivo))
            ->assertOk()
            ->assertDownload('radiografia.png');

        $this->delete(route('expedientes.archivos.destroy', $archivo))
            ->assertSessionHas('exito');
        Storage::disk('local')->assertMissing($archivo->ruta);
        $this->assertDatabaseMissing('expediente_archivo', ['id_archivo' => $archivo->id_archivo]);
    }

    public function test_archivos_rechazan_formatos_no_permitidos_y_recepcionista_no_accede(): void
    {
        Storage::fake('local');
        $doctor = $this->odontologo();
        $expediente = Expediente::create(['id_paciente' => $this->paciente()->id_paciente]);

        $this->actingAs($doctor)
            ->post(route('expedientes.archivos.store', $expediente), [
                'archivo' => UploadedFile::fake()->create('programa.exe', 10, 'application/x-msdownload'),
            ])
            ->assertSessionHasErrors('archivo');

        $this->actingAs($this->usuario('RECEPCIONISTA'))
            ->get(route('expedientes.show', $expediente->paciente))
            ->assertForbidden();
    }

    public function test_tratamiento_y_evoluciones_se_persisten_y_se_validan(): void
    {
        $doctor = $this->odontologo();
        $expediente = Expediente::create(['id_paciente' => $this->paciente()->id_paciente]);
        $this->actingAs($doctor);

        $datos = [
            'diagnostico' => 'Caries en pieza 11',
            'plan' => 'Restauración con resina',
            'estado' => Tratamiento::PLANIFICADO,
            'fecha_inicio' => today()->toDateString(),
        ];
        $this->get(route('tratamientos.create', $expediente))
            ->assertOk()
            ->assertSee('Nuevo tratamiento');

        $this->post(route('tratamientos.store', $expediente), $datos)
            ->assertSessionHasNoErrors();
        $tratamiento = Tratamiento::firstOrFail();

        $this->assertDatabaseHas('tratamiento', [
            'id_tratamiento' => $tratamiento->id_tratamiento,
            'id_expediente' => $expediente->id_expediente,
            'diagnostico' => 'Caries en pieza 11',
        ]);

        $this->put(route('tratamientos.update', $tratamiento), array_replace($datos, [
            'estado' => Tratamiento::EN_CURSO,
        ]))->assertSessionHas('exito');

        $this->get(route('tratamientos.edit', $tratamiento))
            ->assertOk()
            ->assertSee('Editar tratamiento');

        $this->post(route('tratamientos.evoluciones.store', $tratamiento), [
            'observacion' => 'Se realiza limpieza y preparación de la pieza.',
        ])->assertSessionHas('exito');

        $this->get(route('tratamientos.show', $tratamiento))
            ->assertOk()
            ->assertSee('Se realiza limpieza')
            ->assertSee($doctor->nombre);

        $this->delete(route('tratamientos.destroy', $tratamiento))
            ->assertSessionHasErrors('tratamiento');
        $this->assertDatabaseHas('tratamiento', ['id_tratamiento' => $tratamiento->id_tratamiento]);

        $this->post(route('tratamientos.evoluciones.store', $tratamiento), [
            'observacion' => 'x',
        ])->assertSessionHasErrors('observacion');

        $this->post(route('tratamientos.store', $expediente), array_replace($datos, [
            'fecha_inicio' => today()->addDay()->toDateString(),
        ]))->assertSessionHasErrors('fecha_inicio');

        $tratamientoSinEvolucion = $expediente->tratamientos()->create($datos);
        $this->delete(route('tratamientos.destroy', $tratamientoSinEvolucion))
            ->assertSessionHas('exito');
        $this->assertDatabaseMissing('tratamiento', [
            'id_tratamiento' => $tratamientoSinEvolucion->id_tratamiento,
        ]);
    }

    public function test_reportes_tienen_filtros_resumen_exportacion_y_alcance_por_odontologo(): void
    {
        $doctorA = $this->odontologo();
        $doctorB = $this->odontologo();
        $consultorio = Consultorio::create(['nombre' => 'Consultorio de reporte', 'activo' => true]);
        $paciente = $this->paciente();
        $inicio = today()->setTime(10, 0);

        foreach ([$doctorA, $doctorB] as $doctor) {
            Cita::create([
                'id_paciente' => $paciente->id_paciente,
                'id_odontologo' => $doctor->odontologo->id_odontologo,
                'id_consultorio' => $consultorio->id_consultorio,
                'inicio' => $inicio,
                'fin' => $inicio->copy()->addMinutes(30),
                'estado' => Cita::PROGRAMADA,
                'motivo' => 'Control',
            ]);
        }

        $query = ['desde' => today()->toDateString(), 'hasta' => today()->toDateString()];
        $this->actingAs($doctorA)
            ->get(route('reportes.index', $query))
            ->assertOk()
            ->assertSee('Total de citas')
            ->assertSee('1')
            ->assertSee('graficaCitas');

        $this->get(route('reportes.excel', $query))
            ->assertOk()
            ->assertDownload();

        $this->get(route('reportes.pdf', $query))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $this->actingAs($this->usuario('RECEPCIONISTA'))
            ->get(route('reportes.index', $query))
            ->assertOk()
            ->assertSee('Total de citas')
            ->assertSee('2');

        $this->get(route('reportes.index', [
            'desde' => today()->toDateString(),
            'hasta' => today()->subDay()->toDateString(),
        ]))->assertSessionHasErrors('hasta');
    }

    public function test_usuario_con_rol_odontologo_pero_sin_perfil_no_accede_a_expedientes(): void
    {
        $paciente = $this->paciente();

        $this->actingAs($this->usuario())
            ->get(route('expedientes.show', $paciente))
            ->assertForbidden();
    }

    public function test_usuario_sin_perfil_no_ve_enlaces_a_funciones_clinicas_restringidas(): void
    {
        $usuario = $this->usuario();
        $paciente = $this->paciente();

        $this->actingAs($usuario)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('Expedientes')
            ->assertDontSee('Abrir un expediente')
            ->assertDontSee('Reportes');

        $this->get(route('pacientes.show', $paciente))
            ->assertOk()
            ->assertDontSee('Abrir expediente clínico')
            ->assertDontSee('Ver odontograma');
    }
}
