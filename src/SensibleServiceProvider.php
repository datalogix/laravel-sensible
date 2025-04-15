<?php

namespace Datalogix\Sensible;

use Illuminate\Support\ServiceProvider;

class SensibleServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     */
    public function register(): void
    {
        $this->app->alias(SensibleManager::class, 'sensible');
        $this->app->singleton(SensibleManager::class);

        $this->mergeConfigFrom(__DIR__.'/../config/sensible.php', 'sensible');
    }

    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        app('sensible')->run();

        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__.'/../config/sensible.php' => config_path('sensible.php')], 'config');
        }
    }
}
