<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Expediente extends Model
{
    protected $table = 'expediente';
    protected $primaryKey = 'id_expediente';
    public $timestamps = false;

    protected $fillable = ['id_paciente'];

    protected function casts(): array
    {
        return ['creado_en' => 'datetime'];
    }

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class, 'id_paciente', 'id_paciente');
    }

    public function anamnesis(): HasOne
    {
        return $this->hasOne(Anamnesis::class, 'id_expediente', 'id_expediente');
    }

    public function tratamientos(): HasMany
    {
        return $this->hasMany(Tratamiento::class, 'id_expediente', 'id_expediente');
    }

    public function registrosOdontograma(): HasMany
    {
        return $this->hasMany(RegistroOdontograma::class, 'id_expediente', 'id_expediente');
    }
}
