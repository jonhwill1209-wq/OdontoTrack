<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Se lanza cuando el odontólogo o el consultorio ya tienen una cita
 * PROGRAMADA que se superpone con el intervalo solicitado.
 */
class HorarioOcupadoException extends RuntimeException
{
}
