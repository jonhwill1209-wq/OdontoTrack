<?php

namespace Tests\Feature;

use App\Models\Anamnesis;
use App\Models\EvolucionClinica;
use App\Models\Expediente;
use App\Models\Odontologo;
use App\Models\Paciente;
use App\Models\Tratamiento;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class ExpedienteTest extends TestCase
{
    use DatabaseTransactions;

    private function usuario(string $rol = 'ODONTOLOGO'): Usuario
    {
        return Usuario::create([
            'nombre' => 'Profesional de prueba',
            'correo' => Str::uuid() . '@prueba.test',
            'clave_hash' => Hash::make('Prueba2026!'),
            'rol' => $rol,
            'activo' => true,
        ]);
    }

    private function odontologo(): Usuario
    {
        $usuario = $this->usuario();

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
            'apellidos' => 'Expediente',
            'activo' => true,
        ]);
    }

    private function datosTratamiento(): array
    {
        return [
            'diagnostico' => 'Caries dental',
            'plan' => 'Restauración con resina',
            'estado' => Tratamiento::EN_CURSO,
            'fecha_inicio' => '2026-10-16',
        ];
    }

    public function test_odontologo_abre_y_actualiza_el_expediente(): void
    {
        $paciente = $this->paciente();
        $doctor = $this->odontologo();

        $this->actingAs($doctor)
            ->get(route('expedientes.show', $paciente))
            ->assertOk()
            ->assertSee('Expediente clínico');

        $expediente = Expediente::where('id_paciente', $paciente->id_paciente)->firstOrFail();

        $this->actingAs($doctor)
            ->put(route('expedientes.anamnesis.update', $paciente), [
                'alergias' => 'Penicilina',
                'antecedentes' => 'Hipertensión controlada',
                'medicamentos' => 'Losartán',
            ])
            ->assertRedirect(route('expedientes.show', $paciente))
            ->assertSessionHas('exito');

        $this->assertDatabaseHas('anamnesis', [
            'id_expediente' => $expediente->id_expediente,
            'alergias' => 'Penicilina',
        ]);

        $this->actingAs($doctor)
            ->put(route('expedientes.anamnesis.update', $paciente), [
                'alergias' => 'Ninguna conocida',
                'antecedentes' => 'Hipertensión controlada',
                'medicamentos' => 'Losartán',
            ]);

        $this->assertSame(1, Anamnesis::where('id_expediente', $expediente->id_expediente)->count());
        $this->assertDatabaseHas('anamnesis', [
            'id_expediente' => $expediente->id_expediente,
            'alergias' => 'Ninguna conocida',
        ]);
    }

    public function test_tratamiento_y_evolucion_quedan_vinculados_al_paciente_y_autor(): void
    {
        $paciente = $this->paciente();
        $doctor = $this->odontologo();

        $this->actingAs($doctor)
            ->post(route('expedientes.tratamientos.store', $paciente), $this->datosTratamiento())
            ->assertRedirect(route('expedientes.show', $paciente))
            ->assertSessionHas('exito');

        $expediente = Expediente::where('id_paciente', $paciente->id_paciente)->firstOrFail();
        $tratamiento = Tratamiento::where('id_expediente', $expediente->id_expediente)->firstOrFail();

        $this->actingAs($doctor)
            ->post(route('expedientes.evoluciones.store', [$paciente, $tratamiento]), [
                'observacion' => 'Paciente sin dolor posterior a la limpieza.',
            ])
            ->assertRedirect(route('expedientes.show', $paciente))
            ->assertSessionHas('exito');

        $evolucion = EvolucionClinica::where('id_tratamiento', $tratamiento->id_tratamiento)->firstOrFail();

        $this->assertSame($doctor->odontologo->id_odontologo, $evolucion->id_odontologo);
        $this->assertNotNull($evolucion->fecha_registro);
        $this->assertDatabaseHas('tratamiento', [
            'id_tratamiento' => $tratamiento->id_tratamiento,
            'id_expediente' => $expediente->id_expediente,
            'estado' => Tratamiento::EN_CURSO,
        ]);
    }

    public function test_recepcion_no_puede_consultar_ni_modificar_el_expediente(): void
    {
        $paciente = $this->paciente();
        $recepcion = $this->usuario('RECEPCIONISTA');

        $this->actingAs($recepcion)
            ->get(route('expedientes.show', $paciente))
            ->assertForbidden();

        $this->actingAs($recepcion)
            ->post(route('expedientes.tratamientos.store', $paciente), $this->datosTratamiento())
            ->assertForbidden();

        $this->assertDatabaseMissing('expediente', ['id_paciente' => $paciente->id_paciente]);
    }

    public function test_no_permite_agregar_evolucion_a_tratamiento_de_otro_paciente(): void
    {
        $doctor = $this->odontologo();
        $pacienteA = $this->paciente();
        $pacienteB = $this->paciente();
        $expedienteB = Expediente::create(['id_paciente' => $pacienteB->id_paciente]);
        $tratamientoB = Tratamiento::create([
            'id_expediente' => $expedienteB->id_expediente,
            ...$this->datosTratamiento(),
        ]);

        $this->actingAs($doctor)
            ->post(route('expedientes.evoluciones.store', [$pacienteA, $tratamientoB]), [
                'observacion' => 'Esta nota no debe guardarse.',
            ])
            ->assertNotFound();

        $this->assertSame(0, EvolucionClinica::count());
    }
}
