<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAnamnesisRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->rol === 'ODONTOLOGO'
            && $this->user()?->odontologo !== null;
    }

    public function rules(): array
    {
        return [
            'alergias' => ['nullable', 'string', 'max:5000'],
            'antecedentes' => ['nullable', 'string', 'max:5000'],
            'medicamentos' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'alergias.string' => 'Las alergias deben ser texto.',
            'alergias.max' => 'Las alergias no deben superar 5000 caracteres.',
            'antecedentes.string' => 'Los antecedentes deben ser texto.',
            'antecedentes.max' => 'Los antecedentes no deben superar 5000 caracteres.',
            'medicamentos.string' => 'Los medicamentos deben ser texto.',
            'medicamentos.max' => 'Los medicamentos no deben superar 5000 caracteres.',
        ];
    }
}
