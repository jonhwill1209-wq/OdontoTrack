<?php

namespace App\Models;

use App\Enums\Rol;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Usuario extends Authenticatable
{
    protected $table = 'usuario';
    protected $primaryKey = 'id_usuario';
    public $timestamps = false;

    protected $fillable = ['nombre', 'correo', 'clave_hash', 'rol', 'activo'];
    protected $hidden = ['clave_hash'];

    protected function casts(): array
    {
        return ['activo' => 'boolean'];
    }

    // La contraseña vive en la columna clave_hash (no en "password")
    public function getAuthPasswordName(): string
    {
        return 'clave_hash';
    }

    public function getAuthPassword(): string
    {
        return $this->clave_hash;
    }

    // La tabla no tiene remember_token
    public function getRememberTokenName(): string
    {
        return '';
    }

    public function odontologo(): HasOne
    {
        return $this->hasOne(Odontologo::class, 'id_usuario', 'id_usuario');
    }

    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('activo', true);
    }

    protected function rolEtiqueta(): Attribute
    {
        return Attribute::get(fn () => Rol::tryFrom((string) $this->rol)?->etiqueta() ?? (string) $this->rol);
    }

    public function esOdontologo(): bool
    {
        return $this->rol === Rol::Odontologo->value;
    }

    public function esRecepcionista(): bool
    {
        return $this->rol === Rol::Recepcionista->value;
    }
}
