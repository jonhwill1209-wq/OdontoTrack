<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Odontologo extends Model
{
    protected $table = 'odontologo';
    protected $primaryKey = 'id_odontologo';
    public $timestamps = false;

    protected $fillable = ['id_usuario', 'numero_colegiado'];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    public function citas(): HasMany
    {
        return $this->hasMany(Cita::class, 'id_odontologo', 'id_odontologo');
    }
}
