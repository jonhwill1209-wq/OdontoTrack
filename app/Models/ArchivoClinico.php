<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArchivoClinico extends Model
{
    protected $table = 'archivo_clinico';

    public $timestamps = false;

    protected $fillable = ['id_expediente', 'id_odontologo', 'nombre_original', 'ruta', 'mime', 'tamano', 'descripcion', 'fecha'];

    protected function casts(): array
    {
        return ['fecha' => 'datetime'];
    }

    public function expediente()
    {
        return $this->belongsTo(Expediente::class, 'id_expediente', 'id_expediente');
    }

    public function odontologo()
    {
        return $this->belongsTo(Odontologo::class, 'id_odontologo', 'id_odontologo');
    }
}
