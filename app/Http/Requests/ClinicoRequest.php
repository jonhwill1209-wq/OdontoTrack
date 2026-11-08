<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClinicoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->rol === 'ODONTOLOGO' && $this->user()?->odontologo !== null;
    }

    public function rules(): array
    {
        return match ($this->route()->getName()) {
            'expedientes.anamnesis' => ['alergias' => 'nullable|string|max:5000', 'antecedentes' => 'nullable|string|max:5000', 'medicamentos' => 'nullable|string|max:5000'],
            'expedientes.tratamientos.store','expedientes.tratamientos.update' => ['diagnostico' => 'required|string|max:5000', 'plan' => 'required|string|max:5000', 'estado' => 'required|in:PLANIFICADO,EN_CURSO,FINALIZADO', 'fecha_inicio' => 'required|date'],
            'expedientes.evoluciones.store','expedientes.evoluciones.update' => ['observacion' => 'required|string|max:5000'],
            'expedientes.archivos.store' => ['archivo' => 'required|file|mimes:jpg,jpeg,png,pdf|mimetypes:image/jpeg,image/png,application/pdf|max:5120', 'descripcion' => 'required|string|max:500'],
            default => [],
        };
    }
}
