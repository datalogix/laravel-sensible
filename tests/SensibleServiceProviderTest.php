<?php

namespace Datalogix\Sensible\Tests;

use Datalogix\Sensible\Contracts\Configurable;
use Datalogix\Sensible\Sensible;
use Illuminate\Support\ServiceProvider;

class SensibleServiceProviderTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return array_merge(parent::getPackageProviders($app), [AppServiceProvider::class]);
    }

    public function test_runs_configurables_registered_in_application_boot()
    {
        $this->assertTrue(BootRegisteredConfigurable::$configured);
    }
}

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Sensible::register(BootRegisteredConfigurable::class);
    }
}

class BootRegisteredConfigurable implements Configurable
{
    public static bool $configured = false;

    public function enabled(): bool
    {
        return true;
    }

    public function configure(): void
    {
        static::$configured = true;
    }
}
