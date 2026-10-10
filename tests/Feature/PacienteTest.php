<?php

namespace Tests\Feature;

use App\Models\Expediente;
use App\Models\Paciente;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PacienteTest extends TestCase
{
    use DatabaseTransactions;

    private function usuario(string $rol): Usuario
    {
        return Usuario::create([
            'nombre' => 'Usuario de prueba',
            'correo' => strtolower($rol) . '@prueba.test',
            'clave_hash' => Hash::make('Prueba2026!'),
            'rol' => $rol,
            'activo' => true,
        ]);
    }

    private function datosValidos(): array
    {
        return [
            'nombres' => 'Paciente',
            'apellidos' => 'Validacion',
            'fecha_nacimiento' => '2000-01-01',
            'telefono' => '7000-1234',
            'correo' => 'paciente@prueba.test',
        ];
    }

    private function paciente(): Paciente
    {
        return Paciente::create([
            ...$this->datosValidos(),
            'activo' => true,
        ]);
    }

    public function test_recepcion_puede_registrar_un_paciente(): void
    {
        $this->actingAs($this->usuario('RECEPCIONISTA'));

        $respuesta = $this->post(
            route('pacientes.store'),
            $this->datosValidos()
        );

        $paciente = Paciente::where('correo', 'paciente@prueba.test')
            ->firstOrFail();

        $respuesta
            ->assertRedirect(route('pacientes.show', $paciente))
            ->assertSessionHas('exito');

        $this->assertDatabaseHas('paciente', [
            'id_paciente' => $paciente->id_paciente,
            'nombres' => 'Paciente',
            'apellidos' => 'Validacion',
            'activo' => true,
        ]);
    }

    public function test_servidor_rechaza_datos_invalidos_al_registrar(): void
    {
        $this->actingAs($this->usuario('RECEPCIONISTA'));

        $casos = [
            ['nombres', ''],
            ['apellidos', ''],
            ['telefono', 'abcdefg'],
            ['correo', 'correo-invalido'],
            ['fecha_nacimiento', now()->addDay()->toDateString()],
            ['fecha_nacimiento', now()->toDateString()],
        ];

        $cantidadInicial = Paciente::count();

        foreach ($casos as [$campo, $valor]) {
            $datos = $this->datosValidos();
            $datos[$campo] = $valor;

            $this->post(route('pacientes.store'), $datos)
                ->assertSessionHasErrors($campo);
        }

        $this->assertSame($cantidadInicial, Paciente::count());
    }

    public function test_recepcion_puede_actualizar_un_paciente(): void
    {
        $paciente = $this->paciente();

        $this->actingAs($this->usuario('RECEPCIONISTA'));

        $datos = $this->datosValidos();
        $datos['nombres'] = 'Nombre actualizado';

        $this->put(route('pacientes.update', $paciente), $datos)
            ->assertRedirect(route('pacientes.show', $paciente))
            ->assertSessionHas('exito');

        $this->assertDatabaseHas('paciente', [
            'id_paciente' => $paciente->id_paciente,
            'nombres' => 'Nombre actualizado',
        ]);
    }

    public function test_actualizacion_invalida_conserva_los_datos(): void
    {
        $paciente = $this->paciente();

        $this->actingAs($this->usuario('RECEPCIONISTA'));

        $datos = $this->datosValidos();
        $datos['nombres'] = 'Cambio que no debe guardarse';
        $datos['telefono'] = 'abcdefg';

        $this->put(route('pacientes.update', $paciente), $datos)
            ->assertSessionHasErrors('telefono');

        $paciente->refresh();

        $this->assertSame('Paciente', $paciente->nombres);
        $this->assertSame('7000-1234', $paciente->telefono);
    }

    public function test_odontologo_puede_consultar_pero_no_modificar(): void
    {
        $paciente = $this->paciente();

        $this->actingAs($this->usuario('ODONTOLOGO'));

        $this->get(route('pacientes.index'))
            ->assertOk()
            ->assertDontSee('Registrar paciente')
            ->assertDontSee(route('pacientes.edit', $paciente));

        $this->get(route('pacientes.show', $paciente))
            ->assertOk()
            ->assertDontSee('Editar paciente')
            ->assertDontSee('Desactivar paciente')
            ->assertDontSee('Reactivar paciente');

        $this->get(route('pacientes.create'))
            ->assertForbidden();

        $this->get(route('pacientes.edit', $paciente))
            ->assertForbidden();

        $this->post(route('pacientes.store'), $this->datosValidos())
            ->assertForbidden();

        $this->put(
            route('pacientes.update', $paciente),
            array_replace($this->datosValidos(), ['nombres' => 'Alterado'])
        )->assertForbidden();

        $this->delete(route('pacientes.destroy', $paciente))
            ->assertForbidden();

        $paciente->update(['activo' => false]);

        $this->patch(route('pacientes.activar', $paciente))
            ->assertForbidden();

        $paciente->refresh();

        $this->assertSame('Paciente', $paciente->nombres);
        $this->assertFalse($paciente->activo);
        $this->assertSame(1, Paciente::count());
    }

    public function test_desactivar_y_reactivar_conserva_el_expediente(): void
    {
        $paciente = $this->paciente();

        $expediente = Expediente::create([
            'id_paciente' => $paciente->id_paciente,
        ]);

        $this->actingAs($this->usuario('RECEPCIONISTA'));

        $this->delete(route('pacientes.destroy', $paciente))
            ->assertRedirect(route('pacientes.index'))
            ->assertSessionHas('exito');

        $this->assertFalse($paciente->refresh()->activo);

        $this->assertDatabaseHas('expediente', [
            'id_expediente' => $expediente->id_expediente,
            'id_paciente' => $paciente->id_paciente,
        ]);

        $this->patch(route('pacientes.activar', $paciente))
            ->assertRedirect(route('pacientes.show', $paciente))
            ->assertSessionHas('exito');

        $this->assertTrue($paciente->refresh()->activo);

        $this->assertDatabaseHas('expediente', [
            'id_expediente' => $expediente->id_expediente,
            'id_paciente' => $paciente->id_paciente,
        ]);
    }
}
