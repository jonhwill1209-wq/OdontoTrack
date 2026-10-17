<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEvolucionClinicaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->rol === 'ODONTOLOGO'
            && $this->user()?->odontologo !== null;
    }

    public function rules(): array
    {
        return [
            'observacion' => ['required', 'string', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'observacion.required' => 'Ingresa la nota de evolución.',
            'observacion.max' => 'La nota de evolución no debe superar 5000 caracteres.',
        ];
    }
}
