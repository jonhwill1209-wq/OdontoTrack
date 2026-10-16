<?php

namespace Tests\Feature;

use App\Models\Cita;
use App\Models\Consultorio;
use App\Models\Odontologo;
use App\Models\Paciente;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ControlAccesoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Route::middleware(['web', 'auth', 'rol:ODONTOLOGO'])->get('/prueba/expedientes', fn () => 'Zona clínica')->name('expedientes.index');
        Route::middleware(['web', 'auth', 'rol:RECEPCIONISTA'])->get('/prueba/pacientes/crear', fn () => 'Zona de recepción')->name('pacientes.create');
        Route::middleware(['web', 'rol:ODONTOLOGO'])->get('/prueba/sin-auth', fn () => 'No debería verse');
        Route::getRoutes()->refreshNameLookups();
    }

    private function crearUsuario(string $rol, string $nombre = 'Usuario de prueba'): Usuario
    {
        $usuario = Usuario::create([
            'nombre' => $nombre,
            'correo' => uniqid('usuario') . '@prueba.test',
            'clave_hash' => Hash::make('Clave2026!'),
            'rol' => $rol,
            'activo' => true,
        ]);

        if ($rol === 'ODONTOLOGO') {
            Odontologo::create(['id_usuario' => $usuario->id_usuario]);
        }

        return $usuario;
    }

    private function crearCita(Usuario $odontologo, string $paciente, Carbon $inicio): Cita
    {
        return Cita::create([
            'id_paciente' => Paciente::create(['nombres' => $paciente, 'apellidos' => 'Prueba', 'activo' => true])->id_paciente,
            'id_odontologo' => $odontologo->odontologo->id_odontologo,
            'id_consultorio' => Consultorio::firstOrCreate(['nombre' => 'Consultorio 1'], ['activo' => true])->id_consultorio,
            'inicio' => $inicio,
            'fin' => $inicio->copy()->addMinutes(30),
            'estado' => Cita::PROGRAMADA,
        ]);
    }

    public function test_la_recepcionista_recibe_403_en_rutas_del_odontologo(): void
    {
        $this->actingAs($this->crearUsuario('RECEPCIONISTA'))
            ->get('/prueba/expedientes')
            ->assertForbidden()
            ->assertSee('No tienes permiso para acceder a esta sección.')
            ->assertSee('Recepcionista')
            ->assertSee('Cerrar sesión');
    }

    public function test_el_odontologo_entra_a_sus_rutas(): void
    {
        $this->actingAs($this->crearUsuario('ODONTOLOGO'))
            ->get('/prueba/expedientes')
            ->assertOk()
            ->assertSee('Zona clínica');
    }

    public function test_el_odontologo_recibe_403_en_rutas_de_recepcion(): void
    {
        $this->actingAs($this->crearUsuario('ODONTOLOGO'))
            ->get('/prueba/pacientes/crear')
            ->assertForbidden();
    }

    public function test_sin_sesion_una_ruta_con_rol_redirige_al_login(): void
    {
        $this->get('/prueba/sin-auth')->assertRedirect(route('login'));
    }

    public function test_el_menu_y_los_accesos_rapidos_dependen_del_rol(): void
    {
        $this->actingAs($this->crearUsuario('RECEPCIONISTA'))
            ->get(route('dashboard'))
            ->assertSee('Registrar paciente')
            ->assertDontSee('Expedientes');

        $this->actingAs($this->crearUsuario('ODONTOLOGO'))
            ->get(route('dashboard'))
            ->assertSee('Expedientes')
            ->assertDontSee('Registrar paciente');
    }

    public function test_recepcion_ve_todas_las_citas_de_hoy(): void
    {
        $doctora = $this->crearUsuario('ODONTOLOGO', 'Dra. Ana Prueba');
        $doctor = $this->crearUsuario('ODONTOLOGO', 'Dr. Luis Prueba');
        $this->crearCita($doctora, 'Carmela', today()->setTime(9, 0));
        $this->crearCita($doctor, 'Rigoberto', today()->setTime(11, 0));
        $this->crearCita($doctor, 'Anselmo', today()->addDay()->setTime(10, 0));

        $this->actingAs($this->crearUsuario('RECEPCIONISTA'))
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSeeInOrder(['Carmela', 'Rigoberto'])
            ->assertDontSee('Anselmo');
    }

    public function test_el_odontologo_solo_ve_sus_citas_de_hoy(): void
    {
        $doctora = $this->crearUsuario('ODONTOLOGO', 'Dra. Ana Prueba');
        $doctor = $this->crearUsuario('ODONTOLOGO', 'Dr. Luis Prueba');
        $this->crearCita($doctora, 'Carmela', today()->setTime(9, 0));
        $this->crearCita($doctor, 'Rigoberto', today()->setTime(11, 0));

        $this->actingAs($doctora)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Mi agenda de hoy')
            ->assertSee('Carmela')
            ->assertDontSee('Rigoberto');
    }
}
