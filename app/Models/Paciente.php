<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Paciente extends Model
{
    protected $table = 'paciente';
    protected $primaryKey = 'id_paciente';
    public $timestamps = false;

    protected $fillable = ['nombres', 'apellidos', 'fecha_nacimiento', 'telefono', 'correo', 'activo'];

    protected function casts(): array
    {
        return [
            'fecha_nacimiento' => 'date',
            'activo' => 'boolean',
        ];
    }

    protected function nombreCompleto(): Attribute
    {
        return Attribute::get(fn () => trim($this->nombres . ' ' . $this->apellidos));
    }

    // Baja lógica: los pacientes inactivos no se borran, solo se ocultan
    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('activo', true);
    }

    public function citas(): HasMany
    {
        return $this->hasMany(Cita::class, 'id_paciente', 'id_paciente');
    }

    public function expediente(): HasOne
    {
        return $this->hasOne(Expediente::class, 'id_paciente', 'id_paciente');
    }
}
