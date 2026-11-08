<?php

namespace Tests\Feature;

use App\Models\Expediente;
use App\Models\ExpedienteArchivo;
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

class ExpedienteClinicoTest extends TestCase
{
    use RefreshDatabase;

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

    public function test_acceso_y_flujo_clinico(): void
    {
        $p = $this->paciente();
        $this->get(route('expedientes.show', $p))->assertRedirect(route('login'));
        $this->actingAs($this->usuario('RECEPCIONISTA'))->get(route('expedientes.show', $p))->assertForbidden();
        $this->actingAs($this->doctor())->get(route('expedientes.show', $p))->assertOk();
        $expediente = Expediente::where('id_paciente', $p->id_paciente)->firstOrFail();
        $this->put(route('expedientes.anamnesis.update', $expediente), ['alergias' => 'Penicilina'])->assertRedirect();
        $this->assertDatabaseHas('anamnesis', ['alergias' => 'Penicilina']);
        $this->post(route('tratamientos.store', $expediente), ['diagnostico' => 'Caries', 'plan' => 'Restauración', 'estado' => 'EN_CURSO', 'fecha_inicio' => today()->toDateString()])->assertRedirect();
        $t = Tratamiento::firstOrFail();
        $this->post(route('tratamientos.evoluciones.store', $t), ['observacion' => 'Control realizado'])->assertRedirect();
        $this->assertDatabaseHas('evolucion_clinica', ['observacion' => 'Control realizado']);
        $this->get(route('tratamientos.show', $t))->assertOk()->assertSee('Control realizado');
    }

    public function test_archivos_privados_y_validaciones(): void
    {
        Storage::fake('local');
        $p = $this->paciente();
        $this->actingAs($this->doctor());
        $expediente = Expediente::firstOrCreate(['id_paciente' => $p->id_paciente]);
        $url = route('expedientes.archivos.store', $expediente);
        $this->post($url, ['archivo' => UploadedFile::fake()->create('archivo.pdf', 30, 'application/pdf')])->assertRedirect();
        $a = ExpedienteArchivo::firstOrFail();
        Storage::disk('local')->assertExists($a->ruta);
        $this->get(route('expedientes.archivos.download', $a))
            ->assertOk()
            ->assertDownload('archivo.pdf')
            ->assertHeader('X-Content-Type-Options', 'nosniff');
        $this->post($url, ['archivo' => UploadedFile::fake()->create('grande.pdf', 10241, 'application/pdf')])->assertSessionHasErrors('archivo');
        $this->post($url, ['archivo' => UploadedFile::fake()->create('script.html', 1, 'text/html')])->assertSessionHasErrors('archivo');
        $this->actingAs($this->usuario('RECEPCIONISTA'))->get(route('expedientes.archivos.download', $a))->assertForbidden();
    }
}
