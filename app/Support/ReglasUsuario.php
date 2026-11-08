<?php

namespace App\Support;

use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

//Reglas compartidas por los formularios de usuarios, odontólogos y Mi cuenta
class ReglasUsuario
{
    public const PATRON_NOMBRE = '/^[A-Za-zÀ-ÖØ-öø-ÿ][A-Za-zÀ-ÖØ-öø-ÿ.\'\- ]*$/u';
    public const PATRON_CORREO = '/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/';
    public const PATRON_CLAVE = '/^(?=.*\p{Ll})(?=.*\p{Lu})(?=.*\d)(?=.*[^\p{L}\d\s])\S{8,64}$/u';
    public const PATRON_COLEGIADO = '/^[A-Z0-9][A-Z0-9\-]{2,39}$/';

    public static function nombre(): array
    {
        return ['bail', 'required', 'string', 'min:3', 'max:100', 'regex:' . self::PATRON_NOMBRE];
    }

    public static function correo(?int $ignorarUsuario = null): array
    {
        return [
            'bail', 'required', 'string', 'max:150', 'email', 'regex:' . self::PATRON_CORREO,
            Rule::unique('usuario', 'correo')->ignore($ignorarUsuario, 'id_usuario'),
        ];
    }

    public static function clave(bool $obligatoria = true): array
    {
        return ['bail', $obligatoria ? 'required' : 'nullable', 'string', 'regex:' . self::PATRON_CLAVE, 'confirmed'];
    }

    public static function colegiado(?int $ignorarOdontologo = null): array
    {
        return [
            'bail', 'nullable', 'string', 'max:40', 'regex:' . self::PATRON_COLEGIADO,
            Rule::unique('odontologo', 'numero_colegiado')->ignore($ignorarOdontologo, 'id_odontologo'),
        ];
    }

    //Deja los datos en un formato uniforme antes de validar
    public static function normalizar(array $datos): array
    {
        $normalizados = [];

        if (is_string($datos['nombre'] ?? null)) {
            $normalizados['nombre'] = preg_replace('/\s+/u', ' ', trim($datos['nombre']));
        }

        if (is_string($datos['correo'] ?? null)) {
            $normalizados['correo'] = Str::lower(trim($datos['correo']));
        }

        if (is_string($datos['numero_colegiado'] ?? null)) {
            $colegiado = Str::upper(trim($datos['numero_colegiado']));
            $normalizados['numero_colegiado'] = $colegiado === '' ? null : $colegiado;
        }

        return $normalizados;
    }

    public static function mensajes(): array
    {
        return [
            'string' => 'El campo :attribute debe ser texto.',
            'nombre.required' => 'Ingresa el nombre completo.',
            'nombre.min' => 'El nombre debe tener al menos :min caracteres.',
            'nombre.max' => 'El nombre no debe superar :max caracteres.',
            'nombre.regex' => 'El nombre solo puede contener letras, espacios, puntos, apóstrofos y guiones.',
            'correo.required' => 'Ingresa el correo electrónico.',
            'correo.max' => 'El correo no debe superar :max caracteres.',
            'correo.email' => 'Ingresa un correo válido, por ejemplo nombre@clinica.com.',
            'correo.regex' => 'Ingresa un correo válido, por ejemplo nombre@clinica.com.',
            'correo.unique' => 'Ya existe una cuenta registrada con ese correo.',
            'rol.required' => 'Selecciona el rol de la cuenta.',
            'rol.in' => 'Selecciona un rol válido.',
            'numero_colegiado.max' => 'El número de colegiado no debe superar :max caracteres.',
            'numero_colegiado.regex' => 'El número de colegiado solo admite letras, números y guiones, por ejemplo JVPO-1001.',
            'numero_colegiado.unique' => 'Ese número de colegiado ya pertenece a otro odontólogo.',
            'clave.required' => 'Ingresa una contraseña.',
            'clave.regex' => 'La contraseña debe tener de 8 a 64 caracteres sin espacios e incluir mayúscula, minúscula, número y símbolo.',
            'clave.confirmed' => 'La confirmación no coincide con la contraseña.',
        ];
    }

    public static function atributos(): array
    {
        return [
            'nombre' => 'nombre',
            'correo' => 'correo electrónico',
            'rol' => 'rol',
            'numero_colegiado' => 'número de colegiado',
            'clave' => 'contraseña',
            'clave_actual' => 'contraseña actual',
        ];
    }
}
