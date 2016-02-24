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
        $this->publishes([__DIR__.'/config/imagefilelogic.php' => config_path('imagefilelogic.php')]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(
            'Interpro\ImageFileLogic\Concept\Report',
            'Interpro\ImageFileLogic\Laravel\Report'
        );

        $this->app->singleton(
            'Interpro\ImageFileLogic\Concept\ImageConfig',
            'Interpro\ImageFileLogic\Laravel\ImageConfig'
        );

        $this->app->singleton(
            'Interpro\ImageFileLogic\Concept\ActionChainFactory',
            'Interpro\ImageFileLogic\Laravel\ActionChainFactory'
        );

        $this->app->singleton(
            'Interpro\ImageFileLogic\Concept\PathResolver',
            'Interpro\ImageFileLogic\Laravel\PathResolver'
        );

    }

    public function provides()
    {
        //return ['Interpro\ImageFileLogic\ImageFileLogic'];
    }

}

