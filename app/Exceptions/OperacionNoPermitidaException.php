<?php

namespace App\Exceptions;

use RuntimeException;

//Regla de negocio que impide completar una operación sobre un catálogo
class OperacionNoPermitidaException extends RuntimeException
{
}
