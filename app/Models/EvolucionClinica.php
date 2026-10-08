<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvolucionClinica extends Model
{
    protected $table = 'evolucion_clinica';
    protected $primaryKey = 'id_evolucion';
    public $timestamps = false;

    protected $fillable = ['id_tratamiento', 'id_odontologo', 'observacion'];

    protected function casts(): array
    {
        return ['fecha_registro' => 'datetime'];
    }

    public function tratamiento(): BelongsTo
    {
        return $this->belongsTo(Tratamiento::class, 'id_tratamiento', 'id_tratamiento');
    }

    public function odontologo(): BelongsTo
    {
        return $this->belongsTo(Odontologo::class, 'id_odontologo', 'id_odontologo');
    }
}
