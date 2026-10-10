<?php

namespace Tests\Feature;

use App\Models\Expediente;
use App\Models\Odontologo;
use App\Models\Paciente;
use App\Models\RegistroOdontograma;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class OdontogramaTest extends TestCase
{
    use DatabaseTransactions;

    private function usuario(string $rol): Usuario
    {
        return Usuario::create([
            'nombre' => 'Usuario de prueba',
            'correo' => Str::uuid() . '@prueba.test',
            'clave_hash' => Hash::make('Prueba2026!'),
            'rol' => $rol,
            'activo' => true,
        ]);
    }

    private function doctor(): Usuario
    {
        $usuario = $this->usuario('ODONTOLOGO');

        Odontologo::create([
            'id_usuario' => $usuario->id_usuario,
            'numero_colegiado' => 'TEST-' . Str::random(10),
        ]);

        return $usuario;
    }

    private function paciente(): Paciente
    {
        return Paciente::create([
            'nombres' => 'Paciente',
            'apellidos' => 'Odontograma',
            'fecha_nacimiento' => '2000-01-01',
            'telefono' => '7000-1234',
            'correo' => Str::uuid() . '@prueba.test',
            'activo' => true,
        ]);
    }

    private function datosValidos(): array
    {
        return [
            'pieza' => '11',
            'cara' => 'vestibular',
            'condicion' => 'caries',
        ];
    }

    public function test_sin_sesion_redirige_al_login(): void
    {
        $paciente = $this->paciente();
        $cantidad = RegistroOdontograma::count();

        $this->get(route('odontograma.show', $paciente))
            ->assertRedirect(route('login'));

        $this->post(
            route('odontograma.store', $paciente),
            $this->datosValidos()
        )->assertRedirect(route('login'));

        $this->assertSame(
            $cantidad,
            RegistroOdontograma::count()
        );

        $this->assertDatabaseMissing('expediente', [
            'id_paciente' => $paciente->id_paciente,
        ]);
    }

    public function test_recepcion_no_puede_consultar_ni_registrar(): void
    {
        $paciente = $this->paciente();
        $cantidad = RegistroOdontograma::count();

        $this->actingAs($this->usuario('RECEPCIONISTA'));

        $this->get(route('pacientes.show', $paciente))
            ->assertOk()
            ->assertDontSee('Ver odontograma');

        $this->get(route('odontograma.show', $paciente))
            ->assertForbidden();

        $this->post(
            route('odontograma.store', $paciente),
            $this->datosValidos()
        )->assertForbidden();

        $this->assertSame(
            $cantidad,
            RegistroOdontograma::count()
        );

        $this->assertDatabaseMissing('expediente', [
            'id_paciente' => $paciente->id_paciente,
        ]);
    }

    public function test_odontologo_sin_perfil_no_puede_operar(): void
    {
        $paciente = $this->paciente();

        $this->actingAs($this->usuario('ODONTOLOGO'));

        $this->get(route('odontograma.show', $paciente))
            ->assertForbidden();

        $this->post(
            route('odontograma.store', $paciente),
            $this->datosValidos()
        )->assertForbidden();

        $this->assertDatabaseMissing('expediente', [
            'id_paciente' => $paciente->id_paciente,
        ]);
    }

    public function test_registro_usa_el_paciente_y_el_doctor_autenticado(): void
    {
        $paciente = $this->paciente();
        $otroPaciente = $this->paciente();

        $doctor = $this->doctor();
        $otroDoctor = $this->doctor();

        $otroExpediente = Expediente::create([
            'id_paciente' => $otroPaciente->id_paciente,
        ]);

        $this->actingAs($doctor);

        $this->post(
            route('odontograma.store', $paciente),
            array_merge($this->datosValidos(), [
                'id_expediente' => $otroExpediente->id_expediente,
                'id_odontologo' => $otroDoctor->odontologo->id_odontologo,
                'registrado_en' => '2000-01-01 00:00:00',
            ])
        )
            ->assertRedirect(route('odontograma.show', $paciente))
            ->assertSessionHas('exito');

        $expediente = Expediente::where(
            'id_paciente',
            $paciente->id_paciente
        )->firstOrFail();

        $registro = RegistroOdontograma::where(
            'id_expediente',
            $expediente->id_expediente
        )->firstOrFail();

        $this->assertDatabaseHas('registro_odontograma', [
            'id_registro' => $registro->id_registro,
            'id_expediente' => $expediente->id_expediente,
            'id_odontologo' => $doctor->odontologo->id_odontologo,
            'pieza' => '11',
            'cara' => 'vestibular',
            'condicion' => 'caries',
        ]);

        $this->assertNotNull($registro->registrado_en);

        $this->assertNotSame(
            '2000-01-01',
            $registro->registrado_en->format('Y-m-d')
        );

        $this->assertDatabaseMissing('registro_odontograma', [
            'id_expediente' => $otroExpediente->id_expediente,
        ]);

        $this->get(route('odontograma.show', $paciente))
            ->assertOk()
            ->assertSee('Caries')
            ->assertSee($doctor->nombre);
    }

    public function test_rechaza_campos_vacios_y_opciones_invalidas(): void
    {
        $paciente = $this->paciente();
        $cantidad = RegistroOdontograma::count();

        $this->actingAs($this->doctor());

        $casos = [
            ['pieza', ''],
            ['pieza', '99'],
            ['cara', ''],
            ['cara', 'cara_inexistente'],
            ['condicion', ''],
            ['condicion', 'condicion_inexistente'],
        ];

        foreach ($casos as [$campo, $valor]) {
            $this->post(
                route('odontograma.store', $paciente),
                array_replace(
                    $this->datosValidos(),
                    [$campo => $valor]
                )
            )->assertSessionHasErrors($campo);
        }

        $this->assertSame(
            $cantidad,
            RegistroOdontograma::count()
        );

        $this->assertDatabaseMissing('expediente', [
            'id_paciente' => $paciente->id_paciente,
        ]);
    }

    public function test_nueva_condicion_conserva_historial_y_reutiliza_expediente(): void
    {
        $paciente = $this->paciente();

        $this->actingAs($this->doctor());

        $this->post(
            route('odontograma.store', $paciente),
            $this->datosValidos()
        )->assertSessionHas('exito');

        $expediente = Expediente::where(
            'id_paciente',
            $paciente->id_paciente
        )->firstOrFail();

        $this->post(
            route('odontograma.store', $paciente),
            array_replace(
                $this->datosValidos(),
                ['condicion' => 'obturado']
            )
        )->assertSessionHas('exito');

        $this->assertSame(
            1,
            Expediente::where(
                'id_paciente',
                $paciente->id_paciente
            )->count()
        );

        $this->assertSame(
            2,
            RegistroOdontograma::where(
                'id_expediente',
                $expediente->id_expediente
            )->count()
        );

        foreach (['caries', 'obturado'] as $condicion) {
            $this->assertDatabaseHas('registro_odontograma', [
                'id_expediente' => $expediente->id_expediente,
                'pieza' => '11',
                'cara' => 'vestibular',
                'condicion' => $condicion,
            ]);
        }

        $this->get(route('odontograma.show', $paciente))
            ->assertOk()
            ->assertSee('Caries')
            ->assertSee('Obturado')
            ->assertViewHas('registros', function ($registros) {
                return $registros->count() === 2
                    && $registros->first()->condicion === 'obturado';
            });
    }

    public function test_registros_separados_por_paciente_y_conservados_al_desactivar(): void
    {
        $paciente = $this->paciente();
        $otroPaciente = $this->paciente();
        $doctor = $this->doctor();

        $this->actingAs($doctor);

        $this->post(
            route('odontograma.store', $paciente),
            $this->datosValidos()
        )->assertSessionHas('exito');

        $expediente = Expediente::where(
            'id_paciente',
            $paciente->id_paciente
        )->firstOrFail();

        $this->get(route('odontograma.show', $otroPaciente))
            ->assertOk()
            ->assertViewHas(
                'registros',
                fn ($registros) => $registros->isEmpty()
            );

        $this->actingAs($this->usuario('RECEPCIONISTA'));

        $this->delete(route('pacientes.destroy', $paciente))
            ->assertRedirect(route('pacientes.index'));

        $this->assertFalse($paciente->refresh()->activo);

        $this->assertDatabaseHas('expediente', [
            'id_expediente' => $expediente->id_expediente,
            'id_paciente' => $paciente->id_paciente,
        ]);

        $this->assertDatabaseHas('registro_odontograma', [
            'id_expediente' => $expediente->id_expediente,
            'condicion' => 'caries',
        ]);

        $this->actingAs($doctor);

        $this->get(route('odontograma.show', $paciente))
            ->assertOk()
            ->assertSee('Caries')
            ->assertViewHas(
                'registros',
                fn ($registros) => $registros->count() === 1
            );
    }
}
