<?php

namespace Tests\Feature;

use App\Models\Consultorio;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreaCuentas;
use Tests\TestCase;

class ConsultorioTest extends TestCase
{
    use CreaCuentas;
    use RefreshDatabase;

    public function test_el_odontologo_no_gestiona_consultorios(): void
    {
        $consultorio = $this->crearConsultorio();

        $this->actingAs($this->crearOdontologo()->usuario);

        $this->get(route('consultorios.index'))->assertForbidden();
        $this->post(route('consultorios.store'), ['nombre' => 'Consultorio 9'])->assertForbidden();
        $this->delete(route('consultorios.destroy', $consultorio))->assertForbidden();

        $this->assertDatabaseHas('consultorio', ['id_consultorio' => $consultorio->id_consultorio]);
        $this->assertDatabaseMissing('consultorio', ['nombre' => 'Consultorio 9']);
    }

    public function test_registrar_y_editar_un_consultorio(): void
    {
        $this->actingAs($this->crearRecepcionista());

        $this->post(route('consultorios.store'), ['nombre' => '  Consultorio   4 '])
            ->assertSessionHas('exito');

        $consultorio = Consultorio::where('nombre', 'Consultorio 4')->firstOrFail();

        $this->assertTrue($consultorio->activo);

        $this->put(route('consultorios.update', $consultorio), ['nombre' => 'Sala de Ortodoncia #1'])
            ->assertRedirect(route('consultorios.show', $consultorio));

        $this->assertSame('Sala de Ortodoncia #1', $consultorio->fresh()->nombre);
    }

    public function test_valida_nombre_obligatorio_unico_y_con_formato(): void
    {
        $existente = $this->crearConsultorio('Consultorio 1');
        $otro = $this->crearConsultorio('Consultorio 2');
        $this->actingAs($this->crearRecepcionista());

        $this->post(route('consultorios.store'), ['nombre' => ''])
            ->assertSessionHasErrors(['nombre' => 'Ingresa el nombre o número del consultorio.']);

        $this->post(route('consultorios.store'), ['nombre' => 'Consultorio 1'])
            ->assertSessionHasErrors(['nombre' => 'Ya existe un consultorio con ese nombre.']);

        $this->post(route('consultorios.store'), ['nombre' => '<script>'])
            ->assertSessionHasErrors('nombre');

        $this->put(route('consultorios.update', $otro), ['nombre' => 'Consultorio 1'])
            ->assertSessionHasErrors('nombre');

        $this->put(route('consultorios.update', $existente), ['nombre' => 'Consultorio 1'])
            ->assertSessionHasNoErrors();

        $this->assertSame(2, Consultorio::count());
    }

    public function test_listado_con_conteos_busqueda_y_filtro(): void
    {
        $azul = $this->crearConsultorio('Consultorio Azul');
        $this->crearConsultorio('Consultorio Cerrado', false);
        $this->crearCita($this->crearOdontologo(), $azul, now()->addDay()->setTime(11, 0));

        $this->actingAs($this->crearRecepcionista());

        $this->get(route('consultorios.index'))
            ->assertOk()
            ->assertSee('Consultorio Azul')
            ->assertDontSee('Consultorio Cerrado')
            ->assertViewHas('consultorios', fn ($lista) => $lista->first()->citas_pendientes_count === 1);

        $this->get(route('consultorios.index', ['buscar' => 'cerrado', 'estado' => '0']))
            ->assertSee('Consultorio Cerrado')
            ->assertDontSee('Consultorio Azul');
    }

    public function test_desactivar_advierte_si_quedan_citas_y_se_puede_reactivar(): void
    {
        $consultorio = $this->crearConsultorio();
        $this->crearCita($this->crearOdontologo(), $consultorio, now()->addDay()->setTime(9, 0));

        $this->actingAs($this->crearRecepcionista());

        $this->patch(route('consultorios.desactivar', $consultorio))
            ->assertSessionHas('advertencia', 'Tiene 1 cita(s) programada(s). Muévelas a otro consultorio desde la agenda.');

        $this->assertFalse($consultorio->fresh()->activo);

        $this->patch(route('consultorios.activar', $consultorio))->assertSessionHas('exito');

        $this->assertTrue($consultorio->fresh()->activo);
    }

    public function test_solo_se_elimina_un_consultorio_sin_citas(): void
    {
        $vacio = $this->crearConsultorio('Consultorio Vacío');
        $usado = $this->crearConsultorio('Consultorio Usado');
        $this->crearCita($this->crearOdontologo(), $usado, now()->subDay()->setTime(9, 0));

        $this->actingAs($this->crearRecepcionista());

        $this->get(route('consultorios.show', $usado))->assertDontSee('Eliminar definitivamente');

        $this->delete(route('consultorios.destroy', $usado))
            ->assertRedirect(route('consultorios.show', $usado))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('consultorio', ['id_consultorio' => $usado->id_consultorio]);

        $this->get(route('consultorios.show', $vacio))->assertSee('Eliminar definitivamente');

        $this->delete(route('consultorios.destroy', $vacio))
            ->assertRedirect(route('consultorios.index'))
            ->assertSessionHas('exito');

        $this->assertDatabaseMissing('consultorio', ['id_consultorio' => $vacio->id_consultorio]);
    }
}
