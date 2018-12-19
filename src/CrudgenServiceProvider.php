<?php

namespace Hammunima\Crudgen;

use Illuminate\Support\ServiceProvider;

class CrudgenServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/stubs/' => base_path('resources/stubs/'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->commands(
            'Hammunima\Crudgen\Commands\CrudController',
            // 'Appzcoder\CrudGenerator\Commands\CrudControllerCommand',
            // 'Appzcoder\CrudGenerator\Commands\CrudModelCommand',
            // 'Appzcoder\CrudGenerator\Commands\CrudMigrationCommand',
            // 'Appzcoder\CrudGenerator\Commands\CrudViewCommand',
            // 'Appzcoder\CrudGenerator\Commands\CrudLangCommand',
            // 'Appzcoder\CrudGenerator\Commands\CrudApiCommand',
            // 'Appzcoder\CrudGenerator\Commands\CrudApiControllerCommand'
        );
    }
}
