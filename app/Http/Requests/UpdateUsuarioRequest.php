<?php

namespace App\Http\Requests;

use App\Support\ReglasUsuario;
use Illuminate\Foundation\Http\FormRequest;

//El rol no se edita: cambiarlo dejaría citas y expedientes sin un perfil coherente
class UpdateUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('usuario')) === true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(ReglasUsuario::normalizar($this->all()));
    }

    public function rules(): array
    {
        return [
            'nombre' => ReglasUsuario::nombre(),
            'correo' => ReglasUsuario::correo($this->route('usuario')?->id_usuario),
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
