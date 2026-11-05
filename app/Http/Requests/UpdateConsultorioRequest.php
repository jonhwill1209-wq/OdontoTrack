<?php

namespace App\Http\Requests;

class UpdateConsultorioRequest extends StoreConsultorioRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('consultorio')) === true;
    }
}
