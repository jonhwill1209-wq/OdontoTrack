<?php

namespace App\Http\Requests;

use App\Models\Tratamiento;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTratamientoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->rol === 'ODONTOLOGO'
            && $this->user()?->odontologo !== null;
    }

    public function rules(): array
    {
        return [
            'diagnostico' => ['required', 'string', 'max:5000'],
            'plan' => ['required', 'string', 'max:5000'],
            'estado' => [
                'required',
                'string',
                Rule::in([
                    Tratamiento::PLANIFICADO,
                    Tratamiento::EN_CURSO,
                    Tratamiento::FINALIZADO,
                ]),
            ],
            'fecha_inicio' => ['required', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'diagnostico.required' => 'Ingresa el diagnóstico.',
            'diagnostico.max' => 'El diagnóstico no debe superar 5000 caracteres.',
            'plan.required' => 'Ingresa el plan de tratamiento.',
            'plan.max' => 'El plan no debe superar 5000 caracteres.',
            'estado.required' => 'Selecciona el estado del tratamiento.',
            'estado.in' => 'Selecciona un estado válido.',
            'fecha_inicio.required' => 'Ingresa la fecha de inicio.',
            'fecha_inicio.date' => 'Ingresa una fecha de inicio válida.',
        ];
    }
}
