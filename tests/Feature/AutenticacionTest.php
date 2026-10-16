<?php

namespace Tests\Feature;

use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AutenticacionTest extends TestCase
{
    use RefreshDatabase;

    private const CLAVE = 'Clave2026!';

    private function crearUsuario(bool $activo = true): Usuario
    {
        return Usuario::create([
            'nombre' => 'Recepción de prueba',
            'correo' => 'recepcion@prueba.test',
            'clave_hash' => Hash::make(self::CLAVE),
            'rol' => 'RECEPCIONISTA',
            'activo' => $activo,
        ]);
    }

    private function intentarLogin(string $correo, string $clave)
    {
        return $this->from(route('login'))->post(route('login.attempt'), [
            'correo' => $correo,
            'password' => $clave,
        ]);
    }

    public function test_sin_sesion_las_rutas_privadas_redirigen_al_login(): void
    {
        $this->get('/')->assertRedirect('/dashboard');
        $this->get('/dashboard')->assertRedirect(route('login'));
    }

    public function test_la_pantalla_de_login_carga_con_token_csrf(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Inicio de sesión')
            ->assertSee('name="_token"', false);
    }

    public function test_inicia_sesion_con_credenciales_validas(): void
    {
        $usuario = $this->crearUsuario();

        $this->intentarLogin($usuario->correo, self::CLAVE)->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($usuario);
    }

    public function test_rechaza_una_contrasena_incorrecta(): void
    {
        $usuario = $this->crearUsuario();

        $this->intentarLogin($usuario->correo, 'incorrecta')
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors(['correo' => 'Credenciales inválidas.']);

        $this->assertGuest();
    }

    public function test_un_usuario_inactivo_no_puede_entrar(): void
    {
        $usuario = $this->crearUsuario(activo: false);

        $this->intentarLogin($usuario->correo, self::CLAVE)->assertSessionHasErrors('correo');

        $this->assertGuest();
    }

    public function test_valida_los_campos_con_mensajes_en_espanol(): void
    {
        $this->intentarLogin('no-es-un-correo', '')->assertSessionHasErrors([
            'correo' => 'El correo no tiene un formato válido.',
            'password' => 'Ingresa tu contraseña.',
        ]);
    }

    public function test_bloquea_el_acceso_tras_cinco_intentos_fallidos(): void
    {
        $usuario = $this->crearUsuario();

        for ($i = 0; $i < 5; $i++) {
            $this->intentarLogin($usuario->correo, 'incorrecta');
        }

        //Ni siquiera la contraseña correcta entra mientras dura el bloqueo
        $this->intentarLogin($usuario->correo, self::CLAVE)->assertSessionHasErrors('correo');

        $this->assertGuest();
        $this->assertStringContainsString('Demasiados intentos', session('errors')->first('correo'));
    }

    public function test_cerrar_sesion_bloquea_las_rutas_privadas(): void
    {
        $this->actingAs($this->crearUsuario())
            ->post(route('logout'))
            ->assertRedirect(route('login'))
            ->assertSessionHas('exito');

        $this->assertGuest();
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_con_sesion_iniciada_el_login_redirige_al_dashboard(): void
    {
        $this->actingAs($this->crearUsuario())
            ->get(route('login'))
            ->assertRedirect(route('dashboard'));
    }

    public function test_las_paginas_privadas_no_se_guardan_en_cache(): void
    {
        $respuesta = $this->actingAs($this->crearUsuario())->get(route('dashboard'));

        $respuesta->assertOk();
        $this->assertStringContainsString('no-store', $respuesta->headers->get('Cache-Control'));
    }

    public function test_la_ruta_temporal_dev_login_ya_no_existe(): void
    {
        $usuario = $this->crearUsuario();

        $this->get('/dev-login/' . $usuario->correo)->assertNotFound();

        $this->assertGuest();
    }
}
