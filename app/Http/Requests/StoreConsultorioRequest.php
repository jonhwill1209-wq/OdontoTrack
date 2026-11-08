<?php

namespace App\Http\Requests;

use App\Models\Consultorio;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreConsultorioRequest extends FormRequest
{
    public const PATRON_NOMBRE = '/^[\p{L}\p{N}][\p{L}\p{M}\p{N} .#\-]*$/u';

    public function authorize(): bool
    {
        return $this->user()?->can('create', Consultorio::class) === true;
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('nombre'))) {
            $this->merge(['nombre' => preg_replace('/\s+/u', ' ', trim($this->input('nombre')))]);
        }
    }

    public function rules(): array
    {
        return [
            'nombre' => [
                'bail', 'required', 'string', 'min:2', 'max:80', 'regex:' . self::PATRON_NOMBRE,
                Rule::unique('consultorio', 'nombre')->ignore($this->route('consultorio')?->id_consultorio, 'id_consultorio'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'Ingresa el nombre o número del consultorio.',
            'nombre.string' => 'El nombre debe ser texto.',
            'nombre.min' => 'El nombre debe tener al menos :min caracteres.',
            'nombre.max' => 'El nombre no debe superar :max caracteres.',
            'nombre.regex' => 'El nombre solo puede contener letras, números, espacios, puntos, guiones y #.',
            'nombre.unique' => 'Ya existe un consultorio con ese nombre.',
        ];
    }
}
