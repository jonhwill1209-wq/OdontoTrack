<?php

namespace App\Http\Requests;

use App\Support\ReglasUsuario;
use Illuminate\Foundation\Http\FormRequest;

class CambiarClaveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'clave_actual' => ['bail', 'required', 'string', 'current_password'],
            'clave' => [...ReglasUsuario::clave(), 'different:clave_actual'],
        ];
    }

    public function messages(): array
    {
        return [
            ...ReglasUsuario::mensajes(),
            'clave_actual.required' => 'Ingresa tu contraseña actual.',
            'clave_actual.current_password' => 'La contraseña actual no es correcta.',
            'clave.required' => 'Ingresa la nueva contraseña.',
            'clave.different' => 'La nueva contraseña debe ser distinta de la actual.',
        ];
    }

    public function attributes(): array
    {
        return ReglasUsuario::atributos();
    }
}
