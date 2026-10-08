<?php

namespace App\Models;

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

    public function esOdontologo(): bool
    {
        return $this->rol === 'ODONTOLOGO';
    }

    public function esRecepcionista(): bool
    {
        return $this->rol === 'RECEPCIONISTA';
    }
}
