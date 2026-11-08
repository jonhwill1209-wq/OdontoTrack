<?php

namespace App\Http\Requests;

use App\Models\Odontologo;
use App\Support\ReglasUsuario;
use Illuminate\Foundation\Http\FormRequest;

class StoreOdontologoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Odontologo::class) === true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(ReglasUsuario::normalizar($this->all()));
    }

    public function rules(): array
    {
        return [
            'nombre' => ReglasUsuario::nombre(),
            'correo' => ReglasUsuario::correo(),
            'numero_colegiado' => ReglasUsuario::colegiado(),
            'clave' => ReglasUsuario::clave(),
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
