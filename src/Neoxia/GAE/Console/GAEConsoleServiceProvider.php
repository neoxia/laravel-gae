<?php

namespace Neoxia\GAE\Console;

use Illuminate\Support\ServiceProvider;

class GAEConsoleServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app['Neoxia\GAE\Console\CompileAppFile'] = $this->app->share(function ($app) {
            return new CompileAppFile($app['config'], $app['files'], $app['view'], $app['blade.compiler']);
        });

        $this->commands('Neoxia\GAE\Console\CompileAppFile');
    }
}
