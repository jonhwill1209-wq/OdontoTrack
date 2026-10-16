<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

// Se usa tanto para crear como para editar una cita.
class StoreCitaRequest extends FormRequest
{
    public function authorize(): bool
    {
        // El acceso por rol ya lo controla el middleware en routes/modules/citas.php
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id_paciente' => [
                'required',
                Rule::exists('paciente', 'id_paciente')->where('activo', true),
            ],
            'id_odontologo' => ['required', Rule::exists('odontologo', 'id_odontologo')],
            'id_consultorio' => [
                'required',
                Rule::exists('consultorio', 'id_consultorio')->where('activo', true),
            ],
            'inicio' => ['required', 'date'],
            'fin' => ['required', 'date', 'after:inicio'],
            'motivo' => ['nullable', 'string', 'max:250'],
        ];
    }

    public function messages(): array
    {
        return [
            'id_paciente.required' => 'Selecciona un paciente.',
            'id_paciente.exists' => 'El paciente no existe o está dado de baja.',
            'id_odontologo.required' => 'Selecciona un odontólogo.',
            'id_odontologo.exists' => 'El odontólogo seleccionado no existe.',
            'id_consultorio.required' => 'Selecciona un consultorio.',
            'id_consultorio.exists' => 'El consultorio no existe o está inactivo.',
            'inicio.required' => 'Indica la fecha y hora de inicio.',
            'inicio.date' => 'La fecha de inicio no es válida.',
            'fin.required' => 'Indica la fecha y hora de fin.',
            'fin.date' => 'La fecha de fin no es válida.',
            'fin.after' => 'La hora de fin debe ser posterior a la de inicio.',
            'motivo.max' => 'El motivo no puede superar los 250 caracteres.',
        ];
    }
}
