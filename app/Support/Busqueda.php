<?php

namespace App\Support;

class Busqueda
{
    //Escapa % y _ para que se busquen como texto y no como comodines de LIKE
    public static function contiene(string $texto): string
    {
        return '%' . addcslashes($texto, '%_\\') . '%';
    }
}
