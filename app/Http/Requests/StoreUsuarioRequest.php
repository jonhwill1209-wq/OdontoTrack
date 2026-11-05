<?php

namespace App\Http\Requests;

use App\Enums\Rol;
use App\Models\Usuario;
use App\Support\ReglasUsuario;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Usuario::class) === true;
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
            'rol' => ['bail', 'required', 'string', Rule::in(Rol::valores())],
            'numero_colegiado' => ['exclude_unless:rol,' . Rol::Odontologo->value, ...ReglasUsuario::colegiado()],
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
