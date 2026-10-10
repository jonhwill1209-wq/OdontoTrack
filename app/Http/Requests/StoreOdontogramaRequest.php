<?php

namespace App\Http\Requests;

use App\Support\Odontograma;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOdontogramaRequest extends FormRequest
{
    public function authorize(): bool
    {
        $usuario = $this->user();

        return $usuario !== null
            && $usuario->rol === 'ODONTOLOGO'
            && $usuario->odontologo !== null;
    }

    public function rules(): array
    {
        return [
            'pieza' => [
                'required',
                'string',
                Rule::in(Odontograma::piezas()),
            ],
            'cara' => [
                'required',
                'string',
                Rule::in(array_keys(Odontograma::CARAS)),
            ],
            'condicion' => [
                'required',
                'string',
                Rule::in(array_keys(Odontograma::CONDICIONES)),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'pieza.required' => 'Selecciona una pieza dental.',
            'pieza.string' => 'La pieza dental debe ser texto.',
            'pieza.in' => 'Selecciona una pieza dental válida.',

            'cara.required' => 'Selecciona una cara dental.',
            'cara.string' => 'La cara dental debe ser texto.',
            'cara.in' => 'Selecciona una cara dental válida.',

            'condicion.required' => 'Selecciona una condición.',
            'condicion.string' => 'La condición debe ser texto.',
            'condicion.in' => 'Selecciona una condición válida.',
        ];
    }

    public function attributes(): array
    {
        return [
            'pieza' => 'pieza dental',
            'cara' => 'cara dental',
            'condicion' => 'condición',
        ];
    }
}
