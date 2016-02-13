<?php

namespace Interpro\ImageFileLogic;

use Illuminate\Support\ServiceProvider;

class ImageFileLogicServiceProvider extends ServiceProvider {

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //Publishes package config file to applications config folder
        $this->publishes([__DIR__.'/config/resize.php' => config_path('resize.php')]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->make('Interpro\ImageFileLogic\ImageFileController');

        $this->app->singleton('Interpro\ImageFileLogic\ImageFileLogic', function($app)
        {
            return new \Interpro\ImageFileLogic\ImageFileLogic();
        });

        include __DIR__.'/routes.php';
    }

    public function provides()
    {
        return ['Interpro\ImageFileLogic\ImageFileLogic'];
    }

}

