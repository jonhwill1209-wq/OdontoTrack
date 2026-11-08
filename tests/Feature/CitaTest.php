<?php

namespace Tests\Feature;

use App\Exceptions\HorarioOcupadoException;
use App\Models\Cita;
use App\Models\Consultorio;
use App\Models\Odontologo;
use App\Models\Paciente;
use App\Models\Usuario;
use App\Services\CitaService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CitaTest extends TestCase
{
    use DatabaseTransactions;

    private CitaService $servicio;
    private Paciente $paciente;
    private Odontologo $odontologoA;
    private Odontologo $odontologoB;
    private Consultorio $consultorioA;
    private Consultorio $consultorioB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->servicio = app(CitaService::class);
        $this->paciente = Paciente::create([
            'nombres' => 'Paciente', 'apellidos' => 'Agenda', 'activo' => true,
        ]);
        $this->odontologoA = $this->crearOdontologo('a');
        $this->odontologoB = $this->crearOdontologo('b');
        $this->consultorioA = Consultorio::create(['nombre' => 'Sala Test A', 'activo' => true]);
        $this->consultorioB = Consultorio::create(['nombre' => 'Sala Test B', 'activo' => true]);
    }

    private function crearOdontologo(string $sufijo): Odontologo
    {
        $usuario = Usuario::create([
            'nombre' => 'Doctor ' . $sufijo,
            'correo' => "doc-{$sufijo}-" . uniqid() . '@prueba.test',
            'clave_hash' => Hash::make('Prueba2026!'),
            'rol' => 'ODONTOLOGO',
            'activo' => true,
        ]);

        return Odontologo::create([
            'id_usuario' => $usuario->id_usuario,
            'numero_colegiado' => 'T-' . uniqid(),
        ]);
    }

    private function datos(Odontologo $odo, Consultorio $con, string $inicio = '09:00', string $fin = '10:00'): array
    {
        $dia = Carbon::now()->addDays(5)->format('Y-m-d');

        return [
            'id_paciente' => $this->paciente->id_paciente,
            'id_odontologo' => $odo->id_odontologo,
            'id_consultorio' => $con->id_consultorio,
            'inicio' => "$dia $inicio:00",
            'fin' => "$dia $fin:00",
            'motivo' => 'Control',
        ];
    }

    public function test_mismo_odontologo_y_mismo_horario_falla(): void
    {
        $this->servicio->crear($this->datos($this->odontologoA, $this->consultorioA));

        $this->expectException(HorarioOcupadoException::class);
        // Mismo odontólogo, consultorio distinto
        $this->servicio->crear($this->datos($this->odontologoA, $this->consultorioB));
    }

    public function test_mismo_consultorio_falla(): void
    {
        $this->servicio->crear($this->datos($this->odontologoA, $this->consultorioA));

        $this->expectException(HorarioOcupadoException::class);
        // Mismo consultorio, odontólogo distinto
        $this->servicio->crear($this->datos($this->odontologoB, $this->consultorioA));
    }

    public function test_distinto_odontologo_y_consultorio_funciona(): void
    {
        $this->servicio->crear($this->datos($this->odontologoA, $this->consultorioA));
        $segunda = $this->servicio->crear($this->datos($this->odontologoB, $this->consultorioB));

        $this->assertDatabaseHas('cita', [
            'id_cita' => $segunda->id_cita,
            'estado' => Cita::PROGRAMADA,
        ]);
        $this->assertSame(2, Cita::where('id_paciente', $this->paciente->id_paciente)->count());
    }

    public function test_cita_cancelada_no_bloquea_el_horario(): void
    {
        $primera = $this->servicio->crear($this->datos($this->odontologoA, $this->consultorioA));
        $this->servicio->cancelar($primera);

        $nueva = $this->servicio->crear($this->datos($this->odontologoA, $this->consultorioA));

        $this->assertDatabaseHas('cita', ['id_cita' => $primera->id_cita, 'estado' => Cita::CANCELADA]);
        $this->assertDatabaseHas('cita', ['id_cita' => $nueva->id_cita, 'estado' => Cita::PROGRAMADA]);
    }

    public function test_editar_sin_cambiar_horario_no_choca_consigo_misma(): void
    {
        $cita = $this->servicio->crear($this->datos($this->odontologoA, $this->consultorioA));

        $datos = $this->datos($this->odontologoA, $this->consultorioA);
        $datos['motivo'] = 'Motivo actualizado';
        $this->servicio->actualizar($cita, $datos);

        $this->assertDatabaseHas('cita', [
            'id_cita' => $cita->id_cita,
            'motivo' => 'Motivo actualizado',
        ]);
    }

    public function test_citas_consecutivas_sin_empalme_son_validas(): void
    {
        $this->servicio->crear($this->datos($this->odontologoA, $this->consultorioA, '09:00', '10:00'));
        $segunda = $this->servicio->crear($this->datos($this->odontologoA, $this->consultorioA, '10:00', '11:00'));

        $this->assertNotNull($segunda->id_cita);
    }

    public function test_formulario_muestra_error_al_agendar_horario_ocupado(): void
    {
        $this->servicio->crear($this->datos($this->odontologoA, $this->consultorioA));

        $recepcion = Usuario::create([
            'nombre' => 'Recepción test',
            'correo' => 'recep-' . uniqid() . '@prueba.test',
            'clave_hash' => Hash::make('Prueba2026!'),
            'rol' => 'RECEPCIONISTA',
            'activo' => true,
        ]);

        $respuesta = $this->actingAs($recepcion)
            ->from(route('citas.create'))
            ->post(route('citas.store'), $this->datos($this->odontologoA, $this->consultorioB));

        $respuesta->assertRedirect(route('citas.create'))
            ->assertSessionHasErrors('inicio');
    }

    public function test_cancelar_por_ruta_solo_cambia_el_estado(): void
    {
        $cita = $this->servicio->crear($this->datos($this->odontologoA, $this->consultorioA));

        $this->actingAs($this->odontologoA->usuario)
            ->patch(route('citas.cancelar', $cita))
            ->assertRedirect(route('citas.index'))
            ->assertSessionHas('exito');

        $this->assertDatabaseHas('cita', ['id_cita' => $cita->id_cita, 'estado' => Cita::CANCELADA]);
    }

    private function recepcion(): Usuario
    {
        return Usuario::create([
            'nombre' => 'Recepción test',
            'correo' => 'recep-' . uniqid() . '@prueba.test',
            'clave_hash' => Hash::make('Prueba2026!'),
            'rol' => 'RECEPCIONISTA',
            'activo' => true,
        ]);
    }

    public function test_no_permite_agendar_en_fecha_pasada(): void
    {
        $datos = $this->datos($this->odontologoA, $this->consultorioA);
        $ayer = Carbon::now()->subDay()->format('Y-m-d');
        $datos['inicio'] = "$ayer 09:00:00";
        $datos['fin'] = "$ayer 10:00:00";

        $this->actingAs($this->recepcion())
            ->from(route('citas.create'))
            ->post(route('citas.store'), $datos)
            ->assertRedirect(route('citas.create'))
            ->assertSessionHasErrors('inicio');

        $this->assertDatabaseMissing('cita', ['inicio' => "$ayer 09:00:00"]);
    }

    public function test_servicio_rechaza_fecha_pasada(): void
    {
        $datos = $this->datos($this->odontologoA, $this->consultorioA);
        $ayer = Carbon::now()->subDay()->format('Y-m-d');
        $datos['inicio'] = "$ayer 09:00:00";
        $datos['fin'] = "$ayer 10:00:00";

        $this->expectException(\InvalidArgumentException::class);
        $this->servicio->crear($datos);
    }

    public function test_fin_debe_ser_posterior_al_inicio(): void
    {
        $datos = $this->datos($this->odontologoA, $this->consultorioA, '10:00', '09:00');

        $this->actingAs($this->recepcion())
            ->from(route('citas.create'))
            ->post(route('citas.store'), $datos)
            ->assertSessionHasErrors('fin');
    }

    public function test_marcar_realizada_cambia_el_estado(): void
    {
        $cita = $this->servicio->crear($this->datos($this->odontologoA, $this->consultorioA));

        $this->actingAs($this->odontologoA->usuario)
            ->patch(route('citas.realizar', $cita))
            ->assertRedirect(route('citas.index'))
            ->assertSessionHas('exito');

        $this->assertDatabaseHas('cita', ['id_cita' => $cita->id_cita, 'estado' => Cita::REALIZADA]);
    }

    public function test_cita_realizada_no_se_puede_cancelar_ni_realizar_de_nuevo(): void
    {
        $cita = $this->servicio->crear($this->datos($this->odontologoA, $this->consultorioA));
        $this->servicio->marcarRealizada($cita);

        $this->actingAs($this->recepcion())
            ->patch(route('citas.cancelar', $cita))
            ->assertSessionHasErrors('estado');

        $this->assertDatabaseHas('cita', ['id_cita' => $cita->id_cita, 'estado' => Cita::REALIZADA]);
    }

    public function test_mover_cita_actualiza_el_horario(): void
    {
        $cita = $this->servicio->crear($this->datos($this->odontologoA, $this->consultorioA));
        $dia = Carbon::now()->addDays(5)->format('Y-m-d');

        $this->actingAs($this->recepcion())
            ->patchJson(route('citas.mover', $cita), [
                'inicio' => Carbon::parse("$dia 14:00:00")->toIso8601String(),
                'fin' => Carbon::parse("$dia 15:00:00")->toIso8601String(),
            ])
            ->assertOk()
            ->assertJsonPath('mensaje', 'Cita reprogramada correctamente.');

        $this->assertDatabaseHas('cita', ['id_cita' => $cita->id_cita, 'inicio' => "$dia 14:00:00"]);
    }

    public function test_mover_cita_a_horario_ocupado_responde_422_y_no_cambia(): void
    {
        $this->servicio->crear($this->datos($this->odontologoA, $this->consultorioA, '09:00', '10:00'));
        $otra = $this->servicio->crear($this->datos($this->odontologoB, $this->consultorioA, '11:00', '12:00'));
        $dia = Carbon::now()->addDays(5)->format('Y-m-d');

        $this->actingAs($this->recepcion())
            ->patchJson(route('citas.mover', $otra), [
                'inicio' => Carbon::parse("$dia 09:30:00")->toIso8601String(),
                'fin' => Carbon::parse("$dia 10:30:00")->toIso8601String(),
            ])
            ->assertStatus(422)
            ->assertJsonStructure(['mensaje']);

        $this->assertDatabaseHas('cita', ['id_cita' => $otra->id_cita, 'inicio' => "$dia 11:00:00"]);
    }

    public function test_mover_cita_al_pasado_responde_422(): void
    {
        $cita = $this->servicio->crear($this->datos($this->odontologoA, $this->consultorioA));
        $ayer = Carbon::now()->subDay()->format('Y-m-d');

        $this->actingAs($this->recepcion())
            ->patchJson(route('citas.mover', $cita), [
                'inicio' => Carbon::parse("$ayer 09:00:00")->toIso8601String(),
                'fin' => Carbon::parse("$ayer 10:00:00")->toIso8601String(),
            ])
            ->assertStatus(422);
    }

    public function test_no_se_mueve_una_cita_cancelada(): void
    {
        $cita = $this->servicio->crear($this->datos($this->odontologoA, $this->consultorioA));
        $this->servicio->cancelar($cita);
        $dia = Carbon::now()->addDays(6)->format('Y-m-d');

        $this->actingAs($this->recepcion())
            ->patchJson(route('citas.mover', $cita), [
                'inicio' => "$dia 09:00:00",
                'fin' => "$dia 10:00:00",
            ])
            ->assertStatus(422);
    }

    public function test_eventos_se_filtran_por_odontologo_y_consultorio(): void
    {
        $this->servicio->crear($this->datos($this->odontologoA, $this->consultorioA, '09:00', '10:00'));
        $this->servicio->crear($this->datos($this->odontologoB, $this->consultorioB, '09:00', '10:00'));

        $usuario = $this->recepcion();

        $porOdontologo = $this->actingAs($usuario)
            ->getJson(route('citas.eventos', ['id_odontologo' => $this->odontologoA->id_odontologo]))
            ->assertOk();
        $this->assertCount(1, $porOdontologo->json());

        $porConsultorio = $this->actingAs($usuario)
            ->getJson(route('citas.eventos', ['id_consultorio' => $this->consultorioB->id_consultorio]))
            ->assertOk();
        $this->assertCount(1, $porConsultorio->json());
        $this->assertNotNull($porConsultorio->json()[0]['extendedProps']['url_realizar']);
    }

    public function test_invitado_no_puede_usar_las_rutas_de_citas(): void
    {
        $cita = $this->servicio->crear($this->datos($this->odontologoA, $this->consultorioA));

        $this->patch(route('citas.realizar', $cita))->assertRedirect(route('login'));
        $this->patchJson(route('citas.mover', $cita), [])->assertUnauthorized();
        $this->get(route('citas.index'))->assertRedirect(route('login'));
    }
}
