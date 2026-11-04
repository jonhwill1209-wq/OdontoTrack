<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePacienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->esRecepcionista() === true;
    }

    public function rules(): array
    {
        return [
            'nombres' => [
                'bail',
                'required',
                'string',
                'max:100',
                'regex:/^\p{L}[\p{L}\p{M}]*(?: +\p{L}[\p{L}\p{M}]*)*$/u',
            ],
            'apellidos' => [
                'bail',
                'required',
                'string',
                'max:100',
                'regex:/^\p{L}[\p{L}\p{M}]*(?: +\p{L}[\p{L}\p{M}]*)*$/u',
            ],
            'fecha_nacimiento' => [
                'bail',
                'nullable',
                'date',
                'before:today',
            ],
            'telefono' => [
                'bail',
                'nullable',
                'string',
                'regex:/^[0-9]{4}-?[0-9]{4}$/',
            ],
            'correo' => [
                'bail',
                'nullable',
                'string',
                'email',
                'max:150',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'El campo :attribute es obligatorio.',
            'string' => 'El campo :attribute debe ser texto.',
            'max' => 'El campo :attribute no debe superar :max caracteres.',
            'nombres.regex' => 'Los nombres solo pueden contener letras y espacios.',
            'apellidos.regex' => 'Los apellidos solo pueden contener letras y espacios.',
            'fecha_nacimiento.date' => 'Ingresa una fecha de nacimiento válida.',
            'fecha_nacimiento.before' => 'La fecha de nacimiento debe ser anterior a hoy.',
            'telefono.regex' => 'Ingresa un teléfono de 8 dígitos, como 7123-4567 o 71234567.',
            'correo.email' => 'Ingresa un correo electrónico válido.',
        ];
    }

    public function attributes(): array
    {
        return [
            'nombres' => 'nombres',
            'apellidos' => 'apellidos',
            'fecha_nacimiento' => 'fecha de nacimiento',
            'telefono' => 'teléfono',
            'correo' => 'correo electrónico',
        ];
    }
}
