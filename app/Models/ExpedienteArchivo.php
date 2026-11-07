<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpedienteArchivo extends Model
{
    protected $table = 'expediente_archivo';

    protected $primaryKey = 'id_archivo';

    public $timestamps = false;

    protected $fillable = [
        'id_expediente',
        'nombre_original',
        'ruta',
        'mime_type',
        'tamano',
        'subido_en',
    ];

    protected function casts(): array
    {
        return [
            'tamano' => 'integer',
            'subido_en' => 'datetime',
        ];
    }

    public function expediente(): BelongsTo
    {
        return $this->belongsTo(Expediente::class, 'id_expediente', 'id_expediente');
    }
}
