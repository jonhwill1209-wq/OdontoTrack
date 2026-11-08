<?php

namespace Tests\Feature;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use InvalidArgumentException;
use RuntimeException;
use Tests\Concerns\CreaCuentas;
use Tests\TestCase;

class PaginasErrorTest extends TestCase
{
    use CreaCuentas;
    use RefreshDatabase;

    public function test_el_404_conserva_el_menu_cuando_hay_sesion(): void
    {
        $this->actingAs($this->crearRecepcionista())
            ->get('/esta-pagina-no-existe')
            ->assertNotFound()
            ->assertSee('Página no encontrada')
            ->assertSee('Cerrar sesión');
    }

    public function test_el_404_de_un_invitado_no_muestra_el_menu(): void
    {
        $this->get('/esta-pagina-no-existe')
            ->assertNotFound()
            ->assertSee('Página no encontrada')
            ->assertDontSee('Cerrar sesión');
    }

    public function test_el_404_de_un_registro_inexistente(): void
    {
        $this->actingAs($this->crearRecepcionista())
            ->get(route('consultorios.show', 999999))
            ->assertNotFound()
            ->assertSee('ya no está disponible');
    }

    public function test_el_403_explica_el_rol_y_ofrece_salidas(): void
    {
        $this->actingAs($this->crearOdontologo()->usuario)
            ->from(route('dashboard'))
            ->get(route('usuarios.index'))
            ->assertForbidden()
            ->assertSee('Acceso denegado')
            ->assertSee('No tienes permiso para acceder a esta sección.')
            ->assertSee('Odontólogo')
            ->assertSee('Regresar')
            ->assertSee('Volver al inicio');
    }

    public function test_el_403_nunca_muestra_el_mensaje_generico_en_ingles(): void
    {
        Route::middleware('web')->get('/prueba/prohibido', fn () => throw new AuthorizationException());

        $this->get('/prueba/prohibido')
            ->assertForbidden()
            ->assertSee('No tienes permiso para acceder a esta sección.')
            ->assertDontSee('This action is unauthorized');
    }

    public function test_el_405_para_metodos_no_permitidos(): void
    {
        $this->actingAs($this->crearRecepcionista())
            ->delete('/usuarios')
            ->assertStatus(405)
            ->assertSee('Acción no permitida');
    }

    public function test_el_500_no_expone_detalles_internos(): void
    {
        config(['app.debug' => false]);
        Route::middleware('web')->get('/prueba/falla', fn () => throw new RuntimeException('SQLSTATE detalle interno'));

        $this->get('/prueba/falla')
            ->assertStatus(500)
            ->assertSee('Algo salió mal')
            ->assertDontSee('SQLSTATE');
    }

    public function test_la_pagina_de_mantenimiento(): void
    {
        $this->view('errors.503')->assertSee('Sistema en mantenimiento');
    }

    public function test_un_rol_mal_escrito_en_una_ruta_se_detecta(): void
    {
        Route::middleware(['web', 'auth', 'rol:ADMINISTRADOR'])->get('/prueba/rol-inexistente', fn () => 'nunca');

        $this->withoutExceptionHandling();
        $this->expectException(InvalidArgumentException::class);

        $this->actingAs($this->crearRecepcionista())->get('/prueba/rol-inexistente');
    }
}
