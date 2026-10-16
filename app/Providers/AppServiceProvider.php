<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

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
        Gate::define('admin-only', function ($user) {
        return $user->hasRole('Administrador');
        });

        Gate::define('consulta-turnos', function ($user) {
            return $user->hasAnyRole(['Administrador', 'Colaborador']);
        });

        Gate::define('colaborador-only', function ($user) {
            return $user->hasRole('Colaborador');
        });

    }

}