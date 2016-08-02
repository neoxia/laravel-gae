<?php

namespace Neoxia\GAE\Filesystem;

use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;

class GAEFilesystemServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->make('filesystem')->extend('gcs', function ($app, $config) {
            return new Filesystem(new GCSAdapter($config['root']));
        });
    }
}
