<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    //El correo se compara siempre en minúsculas
    protected function prepareForValidation(): void
    {
        if (is_string($this->input('correo'))) {
            $this->merge(['correo' => Str::lower(trim($this->input('correo')))]);
        }
    }

    public function rules(): array
    {
        return [
            'correo' => ['bail', 'required', 'string', 'email', 'max:150'],
            'password' => ['bail', 'required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'correo.required' => 'Ingresa tu correo.',
            'correo.string' => 'El correo no tiene un formato válido.',
            'correo.email' => 'El correo no tiene un formato válido.',
            'correo.max' => 'El correo no debe superar 150 caracteres.',
            'password.required' => 'Ingresa tu contraseña.',
            'password.string' => 'La contraseña no es válida.',
            'password.max' => 'La contraseña no es válida.',
        ];
    }
}
