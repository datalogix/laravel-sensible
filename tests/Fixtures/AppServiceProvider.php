<?php

namespace Datalogix\Sensible\Tests\Fixtures;

use Datalogix\Sensible\Sensible;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Sensible::register(BootRegisteredConfigurable::class);
    }
}
