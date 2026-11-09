<?php

namespace Tests\Feature;

use App\Models\Expediente;
use App\Models\Odontologo;
use App\Models\Paciente;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Tests\TestCase;

class ExpedienteMigracionTest extends TestCase
{
    use DatabaseMigrations;

    private function usuario(string $rol): Usuario
    {
        return Usuario::create([
            'nombre' => 'Usuario de prueba',
            'correo' => Str::uuid().'@prueba.test',
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
            'numero_colegiado' => 'TEST-'.Str::random(10),
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
            'correo' => Str::uuid().'@prueba.test',
            'activo' => true,
        ]);
    }

    public function test_migracion_pendiente_recupera_expediente_sin_borrar_datos(): void
    {
        $doctor = $this->doctor();
        $paciente = $this->paciente();
        $expediente = Expediente::create(['id_paciente' => $paciente->id_paciente]);
        $expediente->anamnesis()->create(['alergias' => 'Dato previo a la actualización']);

        // Simula una instalación anterior a la migración de adjuntos.
        // DatabaseMigrations aísla el esquema sin envolver DDL en una transacción MySQL.
        Schema::drop('expediente_archivo');
        DB::table('migrations')
            ->where('migration', '2026_11_07_000001_create_expediente_archivo_table')
            ->delete();
        $this->assertFalse(Schema::hasTable('expediente_archivo'));

        $this->artisan('migrate', ['--force' => true])->assertExitCode(0);
        $this->assertTrue(Schema::hasTable('expediente_archivo'));
        $this->assertDatabaseHas('paciente', ['id_paciente' => $paciente->id_paciente]);
        $this->assertDatabaseHas('anamnesis', [
            'id_expediente' => $expediente->id_expediente,
            'alergias' => 'Dato previo a la actualización',
        ]);
        $this->actingAs($doctor)->get(route('expedientes.show', $paciente))
            ->assertOk()->assertSee('Dato previo a la actualización');

        $this->artisan('migrate', ['--force' => true])->assertExitCode(0);
        $this->assertDatabaseCount('expediente', 1);
    }
}
