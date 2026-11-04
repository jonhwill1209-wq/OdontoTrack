<?php

namespace Tests\Feature;

use App\Models\Paciente;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class PacienteValidacionTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        $usuario = Usuario::create([
            'nombre' => 'Recepción de prueba',
            'correo' => Str::uuid() . '@prueba.test',
            'clave_hash' => Hash::make('Prueba2026!'),
            'rol' => 'RECEPCIONISTA',
            'activo' => true,
        ]);

        $this->actingAs($usuario);
    }

    private function datosValidos(): array
    {
        return [
            'nombres' => 'José Ángel',
            'apellidos' => 'Muñoz García',
            'fecha_nacimiento' => '2000-01-01',
            'telefono' => '7123-4567',
            'correo' => Str::uuid() . '@prueba.test',
        ];
    }

    private function casosInvalidos(): array
    {
        return [
            ['nombres', 'José123'],
            ['nombres', 'José@Ángel'],
            ['apellidos', 'García456'],
            ['apellidos', 'Muñoz<script>'],
            ['telefono', '7123456'],
            ['telefono', '712345678'],
            ['telefono', '712-34567'],
            ['telefono', '+50371234567'],
        ];
    }

    public function test_acepta_tildes_enie_y_telefono_con_o_sin_guion(): void
    {
        foreach (['7123-4567', '71234567'] as $telefono) {
            $datos = $this->datosValidos();
            $datos['telefono'] = $telefono;

            $this->post(route('pacientes.store'), $datos)
                ->assertSessionHasNoErrors()
                ->assertSessionHas('exito');

            $paciente = Paciente::where('correo', $datos['correo'])
                ->firstOrFail();

            $this->assertSame('José Ángel', $paciente->nombres);
            $this->assertSame('Muñoz García', $paciente->apellidos);
            $this->assertSame($telefono, $paciente->telefono);
            $this->assertTrue($paciente->activo);
        }
    }

    public function test_permite_dejar_vacios_los_campos_opcionales(): void
    {
        $datos = [
            'nombres' => 'María José',
            'apellidos' => 'Peña López',
            'fecha_nacimiento' => '',
            'telefono' => '',
            'correo' => '',
        ];

        $cantidadInicial = Paciente::count();

        $this->post(route('pacientes.store'), $datos)
            ->assertSessionHasNoErrors()
            ->assertSessionHas('exito');

        $this->assertSame($cantidadInicial + 1, Paciente::count());

        $this->assertDatabaseHas('paciente', [
            'nombres' => 'María José',
            'apellidos' => 'Peña López',
            'fecha_nacimiento' => null,
            'telefono' => null,
            'correo' => null,
            'activo' => true,
        ]);
    }

    public function test_rechaza_nombres_y_telefonos_invalidos_al_registrar(): void
    {
        $cantidadInicial = Paciente::count();

        foreach ($this->casosInvalidos() as [$campo, $valor]) {
            $datos = $this->datosValidos();
            $datos[$campo] = $valor;

            $this->post(route('pacientes.store'), $datos)
                ->assertSessionHasErrors($campo);

            $this->assertSame($cantidadInicial, Paciente::count());
        }
    }

    public function test_edicion_invalida_no_modifica_el_paciente(): void
    {
        $originales = $this->datosValidos();

        $paciente = Paciente::create([
            ...$originales,
            'activo' => true,
        ]);

        foreach ($this->casosInvalidos() as [$campo, $valor]) {
            $datos = $originales;
            $datos[$campo] = $valor;

            $this->put(route('pacientes.update', $paciente), $datos)
                ->assertSessionHasErrors($campo);

            $paciente->refresh();

            $this->assertSame($originales['nombres'], $paciente->nombres);
            $this->assertSame($originales['apellidos'], $paciente->apellidos);
            $this->assertSame($originales['telefono'], $paciente->telefono);
            $this->assertSame($originales['correo'], $paciente->correo);
            $this->assertTrue($paciente->activo);
        }
    }
}
