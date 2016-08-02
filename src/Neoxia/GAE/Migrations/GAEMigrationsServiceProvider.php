<?php

namespace Neoxia\GAE\Migrations;

use Illuminate\Support\ServiceProvider;

class GAEMigrationsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if (! $this->app->routesAreCached()) {
            require __DIR__.'/routes.php';
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        
    }
}
