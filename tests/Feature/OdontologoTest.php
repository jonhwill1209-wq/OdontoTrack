<?php

namespace Tests\Feature;

use App\Models\Cita;
use App\Models\Odontologo;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\Concerns\CreaCuentas;
use Tests\TestCase;

class OdontologoTest extends TestCase
{
    use CreaCuentas;
    use RefreshDatabase;

    private function datosValidos(array $cambios = []): array
    {
        return [
            'nombre' => 'Dra. Sofía Méndez',
            'correo' => 'sofia.mendez@clinica.test',
            'numero_colegiado' => 'jvpo-4004',
            'clave' => 'Dental2026!',
            'clave_confirmation' => 'Dental2026!',
            ...$cambios,
        ];
    }

    public function test_el_odontologo_no_accede_al_catalogo(): void
    {
        $doctora = $this->crearOdontologo();

        $this->actingAs($doctora->usuario);

        $this->get(route('odontologos.index'))->assertForbidden();
        $this->get(route('odontologos.show', $doctora))->assertForbidden();
        $this->put(route('odontologos.update', $doctora), $this->datosValidos())->assertForbidden();
    }

    public function test_registrar_crea_la_cuenta_y_el_perfil_en_un_paso(): void
    {
        $this->actingAs($this->crearRecepcionista());

        $respuesta = $this->post(route('odontologos.store'), $this->datosValidos());

        $odontologo = Odontologo::where('numero_colegiado', 'JVPO-4004')->firstOrFail();

        $respuesta->assertRedirect(route('odontologos.show', $odontologo))->assertSessionHas('exito');

        $this->assertSame('ODONTOLOGO', $odontologo->usuario->rol);
        $this->assertSame('sofia.mendez@clinica.test', $odontologo->usuario->correo);
        $this->assertTrue(Hash::check('Dental2026!', $odontologo->usuario->clave_hash));
    }

    public function test_el_colegiado_es_opcional_pero_unico_y_con_formato(): void
    {
        $this->crearOdontologo([], 'JVPO-4004');
        $this->actingAs($this->crearRecepcionista());

        $this->post(route('odontologos.store'), $this->datosValidos())
            ->assertSessionHasErrors(['numero_colegiado' => 'Ese número de colegiado ya pertenece a otro odontólogo.']);

        $this->post(route('odontologos.store'), $this->datosValidos(['numero_colegiado' => 'JV PO/1']))
            ->assertSessionHasErrors('numero_colegiado');

        $this->post(route('odontologos.store'), $this->datosValidos(['numero_colegiado' => '']))
            ->assertSessionHasNoErrors();

        $this->assertNull(Usuario::where('correo', 'sofia.mendez@clinica.test')->firstOrFail()->odontologo->numero_colegiado);
    }

    public function test_el_listado_muestra_citas_pendientes_y_filtra(): void
    {
        $ana = $this->crearOdontologo(['nombre' => 'Dra. Ana Torres'], 'COL-111');
        $this->crearOdontologo(['nombre' => 'Dr. Luis Inactivo', 'activo' => false], 'COL-222');
        $consultorio = $this->crearConsultorio();

        $this->crearCita($ana, $consultorio, now()->addDay()->setTime(8, 0));
        $this->crearCita($ana, $consultorio, now()->addDays(2)->setTime(8, 0));
        $this->crearCita($ana, $consultorio, now()->addDays(3)->setTime(8, 0), Cita::CANCELADA);

        $this->actingAs($this->crearRecepcionista());

        $this->get(route('odontologos.index'))
            ->assertOk()
            ->assertSee('Dra. Ana Torres')
            ->assertDontSee('Dr. Luis Inactivo')
            ->assertViewHas('odontologos', fn ($lista) => $lista->first()->citas_pendientes_count === 2);

        $this->get(route('odontologos.index', ['buscar' => 'COL-222', 'estado' => 'todos']))
            ->assertSee('Dr. Luis Inactivo')
            ->assertDontSee('Dra. Ana Torres');
    }

    public function test_editar_actualiza_cuenta_y_perfil_juntos(): void
    {
        $odontologo = $this->crearOdontologo(['nombre' => 'Dr. Antes'], 'COL-100');
        $this->actingAs($this->crearRecepcionista());

        $this->put(route('odontologos.update', $odontologo), [
            'nombre' => 'Dr. Después',
            'correo' => 'despues@clinica.test',
            'numero_colegiado' => 'col-200',
        ])->assertRedirect(route('odontologos.show', $odontologo))->assertSessionHas('exito');

        $odontologo->refresh();

        $this->assertSame('Dr. Después', $odontologo->usuario->nombre);
        $this->assertSame('despues@clinica.test', $odontologo->usuario->correo);
        $this->assertSame('COL-200', $odontologo->numero_colegiado);
        $this->assertTrue(Hash::check($this->claveValida(), $odontologo->usuario->clave_hash));
    }

    public function test_la_ficha_resume_y_lista_las_proximas_citas(): void
    {
        $odontologo = $this->crearOdontologo(['nombre' => 'Dra. Paula Ríos']);
        $consultorio = $this->crearConsultorio('Consultorio Azul');

        $this->crearCita($odontologo, $consultorio, now()->addDay()->setTime(10, 0));
        $this->crearCita($odontologo, $consultorio, now()->subDays(3)->setTime(10, 0), Cita::REALIZADA);
        $this->crearCita($odontologo, $consultorio, now()->subDays(2)->setTime(10, 0), Cita::CANCELADA);

        $this->actingAs($this->crearRecepcionista())
            ->get(route('odontologos.show', $odontologo))
            ->assertOk()
            ->assertSee('Dra. Paula Ríos')
            ->assertSee('Consultorio Azul')
            ->assertViewHas('resumen', ['pendientes' => 1, 'realizadas' => 1, 'canceladas' => 1]);
    }

    public function test_desactivar_conserva_el_historial_y_advierte_de_citas_pendientes(): void
    {
        $odontologo = $this->crearOdontologo();
        $cita = $this->crearCita($odontologo, $this->crearConsultorio(), now()->addDays(2)->setTime(9, 0));

        $this->actingAs($this->crearRecepcionista())
            ->patch(route('odontologos.desactivar', $odontologo))
            ->assertRedirect(route('odontologos.show', $odontologo))
            ->assertSessionHas('advertencia');

        $this->assertFalse($odontologo->usuario->fresh()->activo);
        $this->assertDatabaseHas('cita', ['id_cita' => $cita->id_cita, 'estado' => Cita::PROGRAMADA]);

        $this->patch(route('odontologos.activar', $odontologo))->assertSessionHas('exito');

        $this->assertTrue($odontologo->usuario->fresh()->activo);
    }
}
