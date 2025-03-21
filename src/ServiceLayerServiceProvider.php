<?php

namespace GambitoCorp\LaravelServiceLayer;

use Illuminate\Support\ServiceProvider;

class ServiceLayerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                \GambitoCorp\LaravelServiceLayer\Console\Commands\MakeAllCommand::class,
                \GambitoCorp\LaravelServiceLayer\Console\Commands\MakeInterfaceCommand::class,
                \GambitoCorp\LaravelServiceLayer\Console\Commands\MakeRepositoryCommand::class,
                \GambitoCorp\LaravelServiceLayer\Console\Commands\MakeServiceCommand::class
            ]);

            if ($this->app->runningInConsole()) {
                $this->publishes([
                    __DIR__.'/../stubs' => base_path('stubs'),
                ], 'laravel-service-layer-stubs');
            }
        }
    }

    public function register()
    {
        // Registro de bindings si es necesario
    }
}
