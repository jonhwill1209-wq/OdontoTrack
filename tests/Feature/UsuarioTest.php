<?php

namespace Tests\Feature;

use App\Models\Odontologo;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\Concerns\CreaCuentas;
use Tests\TestCase;

class UsuarioTest extends TestCase
{
    use CreaCuentas;
    use RefreshDatabase;

    private function datosValidos(array $cambios = []): array
    {
        return [
            'nombre' => 'Lucía Hernández',
            'correo' => 'lucia@clinica.test',
            'rol' => 'RECEPCIONISTA',
            'clave' => 'Segura2026#',
            'clave_confirmation' => 'Segura2026#',
            ...$cambios,
        ];
    }

    public function test_sin_sesion_el_modulo_redirige_al_login(): void
    {
        $this->get(route('usuarios.index'))->assertRedirect(route('login'));
        $this->post(route('usuarios.store'), $this->datosValidos())->assertRedirect(route('login'));

        $this->assertDatabaseMissing('usuario', ['correo' => 'lucia@clinica.test']);
    }

    public function test_el_odontologo_no_puede_gestionar_usuarios(): void
    {
        $doctora = $this->crearOdontologo()->usuario;
        $otro = $this->crearRecepcionista();

        $this->actingAs($doctora);

        $this->get(route('usuarios.index'))->assertForbidden()->assertSee('Acceso denegado');
        $this->get(route('usuarios.create'))->assertForbidden();
        $this->post(route('usuarios.store'), $this->datosValidos())->assertForbidden();
        $this->patch(route('usuarios.desactivar', $otro))->assertForbidden();

        $this->assertDatabaseMissing('usuario', ['correo' => 'lucia@clinica.test']);
        $this->assertTrue($otro->fresh()->activo);
    }

    public function test_lista_busca_y_filtra_por_rol_y_estado(): void
    {
        $this->crearRecepcionista(['nombre' => 'Carmen Recepción']);
        $this->crearOdontologo(['nombre' => 'Dr. Mario Pérez', 'correo' => 'mario@clinica.test']);
        $this->crearRecepcionista(['nombre' => 'Rosa Inactiva', 'activo' => false]);

        $this->actingAs($this->crearRecepcionista());

        $this->get(route('usuarios.index'))
            ->assertOk()
            ->assertSee('Carmen Recepción')
            ->assertSee('Dr. Mario Pérez')
            ->assertDontSee('Rosa Inactiva');

        $this->get(route('usuarios.index', ['buscar' => 'mario@']))
            ->assertSee('Dr. Mario Pérez')
            ->assertDontSee('Carmen Recepción');

        $this->get(route('usuarios.index', ['rol' => 'ODONTOLOGO']))
            ->assertSee('Dr. Mario Pérez')
            ->assertDontSee('Carmen Recepción');

        $this->get(route('usuarios.index', ['estado' => '0']))
            ->assertSee('Rosa Inactiva')
            ->assertDontSee('Dr. Mario Pérez');

        $this->get(route('usuarios.index', ['buscar' => '%']))
            ->assertViewHas('usuarios', fn ($lista) => $lista->total() === 0);

        $this->get(route('usuarios.index', ['rol' => 'ADMIN']))->assertSessionHasErrors('rol');
    }

    public function test_recepcion_crea_una_cuenta_con_contrasena_cifrada(): void
    {
        $this->actingAs($this->crearRecepcionista());

        $respuesta = $this->post(route('usuarios.store'), $this->datosValidos([
            'nombre' => '  Lucía   Hernández ',
            'correo' => 'Lucia@Clinica.TEST',
        ]));

        $usuario = Usuario::where('correo', 'lucia@clinica.test')->firstOrFail();

        $respuesta->assertRedirect(route('usuarios.show', $usuario))->assertSessionHas('exito');

        $this->assertSame('Lucía Hernández', $usuario->nombre);
        $this->assertTrue($usuario->activo);
        $this->assertNotSame('Segura2026#', $usuario->clave_hash);
        $this->assertTrue(Hash::check('Segura2026#', $usuario->clave_hash));
        $this->assertNull($usuario->odontologo);
    }

    public function test_una_cuenta_de_odontologo_nace_con_su_perfil_profesional(): void
    {
        $this->actingAs($this->crearRecepcionista());

        $this->post(route('usuarios.store'), $this->datosValidos([
            'correo' => 'nuevo.doctor@clinica.test',
            'rol' => 'ODONTOLOGO',
            'numero_colegiado' => 'jvpo-2001',
        ]))->assertSessionHasNoErrors();

        $usuario = Usuario::where('correo', 'nuevo.doctor@clinica.test')->firstOrFail();

        $this->assertSame('ODONTOLOGO', $usuario->rol);
        $this->assertSame('JVPO-2001', $usuario->odontologo?->numero_colegiado);
    }

    public function test_el_servidor_rechaza_datos_invalidos(): void
    {
        $recepcion = $this->crearRecepcionista(['correo' => 'ocupado@clinica.test']);
        $this->actingAs($recepcion);

        $casos = [
            'nombre' => ['nombre' => 'Lucía 123'],
            'correo' => ['correo' => 'sin-dominio@clinica'],
            'rol' => ['rol' => 'ADMIN'],
            'clave' => ['clave' => 'sencilla', 'clave_confirmation' => 'sencilla'],
        ];

        foreach ($casos as $campo => $cambio) {
            $this->post(route('usuarios.store'), $this->datosValidos($cambio))->assertSessionHasErrors($campo);
        }

        $this->post(route('usuarios.store'), $this->datosValidos(['correo' => 'OCUPADO@clinica.test']))
            ->assertSessionHasErrors(['correo' => 'Ya existe una cuenta registrada con ese correo.']);

        $this->post(route('usuarios.store'), $this->datosValidos(['clave_confirmation' => 'Distinta2026#']))
            ->assertSessionHasErrors(['clave' => 'La confirmación no coincide con la contraseña.']);

        $this->assertSame(1, Usuario::count());
    }

    public function test_las_contrasenas_no_quedan_guardadas_en_la_sesion(): void
    {
        $this->actingAs($this->crearRecepcionista());

        $this->from(route('usuarios.create'))
            ->post(route('usuarios.store'), $this->datosValidos(['nombre' => '']))
            ->assertRedirect(route('usuarios.create'))
            ->assertSessionHasInput('correo', 'lucia@clinica.test')
            ->assertSessionMissing('_old_input.clave')
            ->assertSessionMissing('_old_input.clave_confirmation');
    }

    public function test_editar_sin_contrasena_conserva_la_actual_y_no_cambia_el_rol(): void
    {
        $usuario = $this->crearRecepcionista(['correo' => 'antes@clinica.test']);
        $this->actingAs($this->crearRecepcionista());

        $this->put(route('usuarios.update', $usuario), [
            'nombre' => 'Nombre Corregido',
            'correo' => 'despues@clinica.test',
            'rol' => 'ODONTOLOGO',
            'clave' => '',
            'clave_confirmation' => '',
        ])->assertRedirect(route('usuarios.show', $usuario))->assertSessionHas('exito');

        $usuario->refresh();

        $this->assertSame('Nombre Corregido', $usuario->nombre);
        $this->assertSame('despues@clinica.test', $usuario->correo);
        $this->assertSame('RECEPCIONISTA', $usuario->rol);
        $this->assertTrue(Hash::check($this->claveValida(), $usuario->clave_hash));
    }

    public function test_restablecer_la_contrasena_permite_entrar_con_la_nueva(): void
    {
        $usuario = $this->crearRecepcionista(['correo' => 'olvido@clinica.test']);
        $this->actingAs($this->crearRecepcionista());

        $this->put(route('usuarios.update', $usuario), [
            'nombre' => $usuario->nombre,
            'correo' => $usuario->correo,
            'clave' => 'Nueva2026$',
            'clave_confirmation' => 'Nueva2026$',
        ])->assertSessionHasNoErrors();

        $this->assertTrue(Hash::check('Nueva2026$', $usuario->fresh()->clave_hash));
    }

    public function test_cambiar_la_propia_contrasena_desde_usuarios_no_cierra_la_sesion(): void
    {
        $recepcion = $this->crearRecepcionista();
        $this->actingAs($recepcion)->get(route('usuarios.index'))->assertOk();

        $this->put(route('usuarios.update', $recepcion), [
            'nombre' => $recepcion->nombre,
            'correo' => $recepcion->correo,
            'clave' => 'Propia2026&',
            'clave_confirmation' => 'Propia2026&',
        ])->assertSessionHasNoErrors();

        $this->actingAs($recepcion->fresh())->get(route('usuarios.index'))->assertOk();
        $this->assertAuthenticated();
    }

    public function test_nadie_puede_desactivar_su_propia_cuenta(): void
    {
        $recepcion = $this->crearRecepcionista();
        $this->actingAs($recepcion);

        $this->patch(route('usuarios.desactivar', $recepcion))
            ->assertForbidden()
            ->assertSee('No puedes desactivar tu propia cuenta.');

        $this->assertTrue($recepcion->fresh()->activo);

        $this->get(route('usuarios.show', $recepcion))
            ->assertSee('Esta es tu cuenta')
            ->assertDontSee('Desactivar cuenta');
    }

    public function test_desactivar_cierra_la_sesion_abierta_y_bloquea_el_acceso(): void
    {
        $recepcion = $this->crearRecepcionista();
        $colega = $this->crearRecepcionista(['correo' => 'colega@clinica.test']);

        $this->actingAs($recepcion)
            ->patch(route('usuarios.desactivar', $colega))
            ->assertRedirect(route('usuarios.show', $colega))
            ->assertSessionHas('exito');

        $this->assertFalse($colega->fresh()->activo);

        $this->actingAs($colega->fresh())
            ->get(route('dashboard'))
            ->assertRedirect(route('login'))
            ->assertSessionHas('error', 'Tu cuenta fue desactivada. Comunícate con administración.');

        $this->assertGuest();
    }

    public function test_reactivar_permite_iniciar_sesion_de_nuevo(): void
    {
        $colega = $this->crearRecepcionista(['correo' => 'regresa@clinica.test', 'activo' => false]);

        $this->actingAs($this->crearRecepcionista())
            ->patch(route('usuarios.activar', $colega))
            ->assertSessionHas('exito');

        $this->post(route('logout'));

        $this->post(route('login.attempt'), ['correo' => 'regresa@clinica.test', 'password' => $this->claveValida()])
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($colega->fresh());
    }

    public function test_desactivar_un_odontologo_con_citas_genera_una_advertencia(): void
    {
        $odontologo = $this->crearOdontologo();
        $this->crearCita($odontologo, $this->crearConsultorio(), now()->addDay()->setTime(9, 0));

        $this->actingAs($this->crearRecepcionista())
            ->patch(route('usuarios.desactivar', $odontologo->usuario))
            ->assertSessionHas('advertencia', 'Este odontólogo tiene 1 cita(s) programada(s). Reasígnalas o cancélalas desde la agenda.');

        $this->assertSame(0, Odontologo::activos()->count());
    }

    //DEF-01
    public function test_el_menu_muestra_administracion_solo_a_recepcion(): void
    {
        $this->actingAs($this->crearRecepcionista())
            ->get(route('dashboard'))
            ->assertSee('Administración')
            ->assertSee('href="' . route('usuarios.index') . '"', false)
            ->assertSee('href="' . route('odontologos.index') . '"', false)
            ->assertSee('href="' . route('consultorios.index') . '"', false)
            ->assertSee('href="' . route('cuenta.edit') . '"', false);

        $this->actingAs($this->crearOdontologo()->usuario)
            ->get(route('dashboard'))
            ->assertDontSee('Administración')
            ->assertDontSee('href="' . route('usuarios.index') . '"', false)
            ->assertSee('href="' . route('cuenta.edit') . '"', false);
    }

    //DEF-02
    public function test_acepta_nombres_con_mayusculas_y_tildes(): void
    {
        $this->actingAs($this->crearRecepcionista());

        $nombres = ['Recepcion QA', 'JOSÉ PÉREZ', "Dra. María O'Neil-Núñez", 'prueba usuario'];

        foreach ($nombres as $indice => $nombre) {
            $this->post(route('usuarios.store'), $this->datosValidos([
                'nombre' => $nombre,
                'correo' => "nombre{$indice}@clinica.test",
            ]))->assertSessionHasNoErrors();

            $this->assertDatabaseHas('usuario', ['nombre' => $nombre]);
        }

        foreach (['Ana2', '<b>Ana</b>', '.Ana'] as $invalido) {
            $this->post(route('usuarios.store'), $this->datosValidos(['nombre' => $invalido, 'correo' => 'otro@clinica.test']))
                ->assertSessionHasErrors('nombre');
        }
    }

    public function test_la_ficha_muestra_los_datos_y_el_perfil_profesional(): void
    {
        $odontologo = $this->crearOdontologo(['nombre' => 'Dra. Elena Ruiz'], 'JVPO-3003');

        $this->actingAs($this->crearRecepcionista())
            ->get(route('usuarios.show', $odontologo->usuario))
            ->assertOk()
            ->assertSee('Dra. Elena Ruiz')
            ->assertSee('JVPO-3003')
            ->assertSee(route('odontologos.show', $odontologo));
    }
}
