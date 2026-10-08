<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cita extends Model
{
    public const PROGRAMADA = 'PROGRAMADA';
    public const CANCELADA = 'CANCELADA';
    public const REALIZADA = 'REALIZADA';

    protected $table = 'cita';
    protected $primaryKey = 'id_cita';
    public $timestamps = false;

    protected $fillable = [
        'id_paciente', 'id_odontologo', 'id_consultorio',
        'inicio', 'fin', 'estado', 'motivo',
    ];

    protected function casts(): array
    {
        return [
            'inicio' => 'datetime',
            'fin' => 'datetime',
        ];
    }

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class, 'id_paciente', 'id_paciente');
    }

    public function odontologo(): BelongsTo
    {
        return $this->belongsTo(Odontologo::class, 'id_odontologo', 'id_odontologo');
    }

    public function consultorio(): BelongsTo
    {
        return $this->belongsTo(Consultorio::class, 'id_consultorio', 'id_consultorio');
    }
}
