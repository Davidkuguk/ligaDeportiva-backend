<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Aqui se registrarian servicios generales de la aplicacion
     * si mas adelante hicieran falta.
     */
    public function register(): void
    {
        //
    }

    /**
     * Este metodo sirve para arrancar configuraciones comunes
     * cuando la aplicacion ya se esta iniciando.
     */
    public function boot(): void
    {
        //
    }
}
