<?php

namespace Tests\Feature;

use Illuminate\Routing\Route as Ruta;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Tests\TestCase;

//Auditoría automática: falla si algún módulo agrega una ruta sin sesión o sin rol
class RutasProtegidasTest extends TestCase
{
    private const PUBLICAS = ['/', 'login', 'up', 'storage/{path}'];

    private const SOLO_SESION = ['logout', 'dashboard'];

    private function rutasPrivadas(): array
    {
        return collect(Route::getRoutes()->getRoutes())
            ->reject(fn (Ruta $ruta) => $ruta->isFallback || in_array($ruta->uri(), self::PUBLICAS, true))
            ->values()
            ->all();
    }

    private function descripcion(Ruta $ruta): string
    {
        return implode('|', $ruta->methods()) . ' /' . ltrim($ruta->uri(), '/');
    }

    public function test_toda_ruta_privada_exige_iniciar_sesion(): void
    {
        foreach ($this->rutasPrivadas() as $ruta) {
            $this->assertContains('auth', $ruta->gatherMiddleware(), "La ruta {$this->descripcion($ruta)} no exige iniciar sesión.");
        }
    }

    public function test_toda_ruta_de_un_modulo_exige_un_rol(): void
    {
        foreach ($this->rutasPrivadas() as $ruta) {
            if (in_array($ruta->getName(), self::SOLO_SESION, true)) {
                continue;
            }

            $roles = array_filter($ruta->gatherMiddleware(), fn ($middleware) => is_string($middleware) && str_starts_with($middleware, 'rol:'));

            $this->assertNotEmpty($roles, "La ruta {$this->descripcion($ruta)} no restringe el acceso por rol.");
        }
    }

    public function test_los_catalogos_son_exclusivos_de_recepcion(): void
    {
        $catalogos = collect(Route::getRoutes()->getRoutes())
            ->filter(fn (Ruta $ruta) => Str::is(['usuarios.*', 'odontologos.*', 'consultorios.*'], (string) $ruta->getName()));

        $this->assertCount(25, $catalogos);

        foreach ($catalogos as $ruta) {
            $this->assertContains('rol:RECEPCIONISTA', $ruta->gatherMiddleware(), "La ruta {$this->descripcion($ruta)} debe ser solo para recepción.");
        }
    }

    public function test_sin_sesion_cada_pagina_privada_redirige_al_login(): void
    {
        foreach ($this->rutasPrivadas() as $ruta) {
            if (! in_array('GET', $ruta->methods(), true)) {
                continue;
            }

            $uri = '/' . ltrim(preg_replace('/\{[^}]+\}/', '1', $ruta->uri()), '/');

            $this->get($uri)->assertRedirect(route('login'));
        }
    }

    public function test_los_archivos_privados_no_se_sirven_sin_url_firmada(): void
    {
        $this->get('/storage/expedientes/radiografia.png')->assertForbidden();
    }
}
