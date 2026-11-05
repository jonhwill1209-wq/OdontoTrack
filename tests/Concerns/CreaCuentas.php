<?php

namespace Tests\Concerns;

use App\Models\Cita;
use App\Models\Consultorio;
use App\Models\Odontologo;
use App\Models\Paciente;
use App\Models\Usuario;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

trait CreaCuentas
{
    protected function claveValida(): string
    {
        return 'Clave2026!';
    }

    protected function crearRecepcionista(array $datos = []): Usuario
    {
        return Usuario::create([
            'nombre' => 'Recepción de prueba',
            'correo' => uniqid('recepcion') . '@prueba.test',
            'clave_hash' => Hash::make($this->claveValida()),
            'rol' => 'RECEPCIONISTA',
            'activo' => true,
            ...$datos,
        ]);
    }

    protected function crearOdontologo(array $datos = [], ?string $colegiado = null): Odontologo
    {
        $usuario = Usuario::create([
            'nombre' => 'Dra. Odontóloga de Prueba',
            'correo' => uniqid('doctora') . '@prueba.test',
            'clave_hash' => Hash::make($this->claveValida()),
            'rol' => 'ODONTOLOGO',
            'activo' => true,
            ...$datos,
        ]);

        return Odontologo::create([
            'id_usuario' => $usuario->id_usuario,
            'numero_colegiado' => $colegiado,
        ]);
    }

    protected function crearConsultorio(string $nombre = 'Consultorio de prueba', bool $activo = true): Consultorio
    {
        return Consultorio::create(['nombre' => $nombre, 'activo' => $activo]);
    }

    protected function crearCita(Odontologo $odontologo, Consultorio $consultorio, Carbon $inicio, string $estado = Cita::PROGRAMADA): Cita
    {
        $paciente = Paciente::create([
            'nombres' => 'Paciente',
            'apellidos' => 'De Prueba',
            'activo' => true,
        ]);

        return Cita::create([
            'id_paciente' => $paciente->id_paciente,
            'id_odontologo' => $odontologo->id_odontologo,
            'id_consultorio' => $consultorio->id_consultorio,
            'inicio' => $inicio,
            'fin' => $inicio->copy()->addMinutes(30),
            'estado' => $estado,
        ]);
    }
}
