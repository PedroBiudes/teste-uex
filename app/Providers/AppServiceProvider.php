<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

        //Usuarios
        $this->app->bind('App\Repositories\Interfaces\UsuarioInterface', 'App\Repositories\UsuarioRepository');

    }
}
