<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\Concerns\CreaCuentas;
use Tests\TestCase;

class CuentaTest extends TestCase
{
    use CreaCuentas;
    use RefreshDatabase;

    public function test_ambos_roles_consultan_su_cuenta(): void
    {
        $this->actingAs($this->crearRecepcionista(['nombre' => 'Marta Recepción']))
            ->get(route('cuenta.edit'))
            ->assertOk()
            ->assertSee('Marta Recepción')
            ->assertSee('Cambiar contraseña');

        $doctora = $this->crearOdontologo(['nombre' => 'Dra. Inés Vega'], 'COL-900');

        $this->actingAs($doctora->usuario)
            ->get(route('cuenta.edit'))
            ->assertOk()
            ->assertSee('COL-900');
    }

    public function test_cambiar_la_contrasena_exige_la_actual_y_una_nueva_segura(): void
    {
        $usuario = $this->crearRecepcionista();
        $this->actingAs($usuario);

        $this->put(route('cuenta.clave'), [
            'clave_actual' => 'Incorrecta1!',
            'clave' => 'Nueva2026$',
            'clave_confirmation' => 'Nueva2026$',
        ])->assertSessionHasErrors(['clave_actual' => 'La contraseña actual no es correcta.']);

        $this->put(route('cuenta.clave'), [
            'clave_actual' => $this->claveValida(),
            'clave' => 'debil',
            'clave_confirmation' => 'debil',
        ])->assertSessionHasErrors('clave');

        $this->put(route('cuenta.clave'), [
            'clave_actual' => $this->claveValida(),
            'clave' => $this->claveValida(),
            'clave_confirmation' => $this->claveValida(),
        ])->assertSessionHasErrors(['clave' => 'La nueva contraseña debe ser distinta de la actual.']);

        $this->assertTrue(Hash::check($this->claveValida(), $usuario->fresh()->clave_hash));
    }

    public function test_al_cambiarla_la_sesion_actual_sigue_abierta(): void
    {
        $usuario = $this->crearRecepcionista();
        $this->actingAs($usuario);

        $this->put(route('cuenta.clave'), [
            'clave_actual' => $this->claveValida(),
            'clave' => 'Nueva2026$',
            'clave_confirmation' => 'Nueva2026$',
        ])->assertRedirect(route('cuenta.edit'))->assertSessionHas('exito');

        $this->assertTrue(Hash::check('Nueva2026$', $usuario->fresh()->clave_hash));

        $this->get(route('dashboard'))->assertOk();
        $this->assertAuthenticatedAs($usuario);
    }

    public function test_si_la_contrasena_cambia_en_otro_lugar_se_cierra_esta_sesion(): void
    {
        $usuario = $this->crearRecepcionista();

        $this->actingAs($usuario)->get(route('dashboard'))->assertOk();

        //Simula que administración la restableció desde otro equipo
        $usuario->update(['clave_hash' => Hash::make('Otra2026$')]);

        $this->get(route('dashboard'))
            ->assertRedirect(route('login'))
            ->assertSessionHas('error', 'Tu contraseña cambió. Inicia sesión con la nueva contraseña.');

        $this->assertGuest();
    }
}
