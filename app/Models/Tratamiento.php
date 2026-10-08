<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tratamiento extends Model
{
    public const PLANIFICADO = 'PLANIFICADO';
    public const EN_CURSO = 'EN_CURSO';
    public const FINALIZADO = 'FINALIZADO';

    protected $table = 'tratamiento';
    protected $primaryKey = 'id_tratamiento';
    public $timestamps = false;

    protected $fillable = ['id_expediente', 'diagnostico', 'plan', 'estado', 'fecha_inicio'];

    protected function casts(): array
    {
        return ['fecha_inicio' => 'date'];
    }

    public function expediente(): BelongsTo
    {
        return $this->belongsTo(Expediente::class, 'id_expediente', 'id_expediente');
    }

    public function evoluciones(): HasMany
    {
        return $this->hasMany(EvolucionClinica::class, 'id_tratamiento', 'id_tratamiento');
    }
}
