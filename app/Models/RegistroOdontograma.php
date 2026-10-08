<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegistroOdontograma extends Model
{
    protected $table = 'registro_odontograma';
    protected $primaryKey = 'id_registro';
    public $timestamps = false;

    protected $fillable = ['id_expediente', 'id_odontologo', 'pieza', 'cara', 'condicion'];

    protected function casts(): array
    {
        return ['registrado_en' => 'datetime'];
    }

    public function expediente(): BelongsTo
    {
        return $this->belongsTo(Expediente::class, 'id_expediente', 'id_expediente');
    }

    public function odontologo(): BelongsTo
    {
        return $this->belongsTo(Odontologo::class, 'id_odontologo', 'id_odontologo');
    }
}
