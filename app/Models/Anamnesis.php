<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Anamnesis extends Model
{
    protected $table = 'anamnesis';
    protected $primaryKey = 'id_anamnesis';
    public $timestamps = false;

    protected $fillable = ['id_expediente', 'alergias', 'antecedentes', 'medicamentos'];

    protected function casts(): array
    {
        return ['actualizado_en' => 'datetime'];
    }

    public function expediente(): BelongsTo
    {
        return $this->belongsTo(Expediente::class, 'id_expediente', 'id_expediente');
    }
}
