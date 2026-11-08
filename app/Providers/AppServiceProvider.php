<?php

namespace App\Providers;

use App\Models\Consultorio;
use App\Models\Odontologo;
use App\Models\Usuario;
use App\Policies\ConsultorioPolicy;
use App\Policies\OdontologoPolicy;
use App\Policies\UsuarioPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Usuario::class, UsuarioPolicy::class);
        Gate::policy(Odontologo::class, OdontologoPolicy::class);
        Gate::policy(Consultorio::class, ConsultorioPolicy::class);
    }
}
