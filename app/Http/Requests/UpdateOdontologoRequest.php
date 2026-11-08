<?php

namespace App\Http\Requests;

use App\Support\ReglasUsuario;
use Illuminate\Foundation\Http\FormRequest;

class UpdateOdontologoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('odontologo')) === true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(ReglasUsuario::normalizar($this->all()));
    }

    public function rules(): array
    {
        $odontologo = $this->route('odontologo');

        return [
            'nombre' => ReglasUsuario::nombre(),
            'correo' => ReglasUsuario::correo($odontologo?->id_usuario),
            'numero_colegiado' => ReglasUsuario::colegiado($odontologo?->id_odontologo),
            'clave' => ReglasUsuario::clave(obligatoria: false),
        ];
    }

    public function messages(): array
    {
        return ReglasUsuario::mensajes();
    }

    public function attributes(): array
    {
        return ReglasUsuario::atributos();
    }
}
