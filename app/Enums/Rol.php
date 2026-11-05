<?php

namespace App\Enums;

enum Rol: string
{
    case Recepcionista = 'RECEPCIONISTA';
    case Odontologo = 'ODONTOLOGO';

    public function etiqueta(): string
    {
        return match ($this) {
            self::Recepcionista => 'Recepcionista / Administrador',
            self::Odontologo => 'Odontólogo',
        };
    }

    public static function valores(): array
    {
        return array_column(self::cases(), 'value');
    }
}
