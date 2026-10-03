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
        // 🔒 SUPER ADMIN: Si el usuario tiene 'acceso_total', autoriza automáticamente
        Gate::before(function ($usuario, $permiso) {
            // Verificamos si el usuario tiene asignado el permiso global en el guard de la API
            return $usuario->hasPermissionTo('acceso_total', 'api') ? true : null;
        });
    }
}
