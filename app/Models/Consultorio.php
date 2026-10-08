<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Consultorio extends Model
{
    protected $table = 'consultorio';
    protected $primaryKey = 'id_consultorio';
    public $timestamps = false;

    protected $fillable = ['nombre', 'activo'];

    protected function casts(): array
    {
        return ['activo' => 'boolean'];
    }

    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('activo', true);
    }

    public function citas(): HasMany
    {
        return $this->hasMany(Cita::class, 'id_consultorio', 'id_consultorio');
    }
}
