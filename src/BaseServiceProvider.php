<?php

namespace Pixan\Base;

use Illuminate\Support\ServiceProvider;

class BaseServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        $this->publishes([
            __DIR__ . '/migrations' => $this->app->databasePath() . '/migrations'
        ], 'migrations');
        $this->publishes([
            __DIR__ . '/seeds' => $this->app->databasePath() . '/seeds'
        ], 'seeds');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
        include __DIR__.'/routes.php';
        include __DIR__.'/helpers.php';
        $this->app->make('Pixan\Base\ApiController');
    }
}