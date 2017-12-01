<?php

namespace Davidcb\Esendex;

use Illuminate\Support\ServiceProvider;

class EsendexServiceProvider extends ServiceProvider
{
    protected $defer = true;

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../config/esendex.php' => config_path('esendex.php'),
        ], 'config');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/esendex.php', 'esendex');

        $this->app->singleton('esendex', function ($app) {
            return new Esendex($app);
        });
    }

    public function provides()
    {
        return ['esendex'];
    }
}
