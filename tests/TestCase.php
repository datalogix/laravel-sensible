<?php

namespace Datalogix\Sensible\Tests;

use Datalogix\Sensible\SensibleServiceProvider;
use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    /**
     * @param  Application  $app
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [SensibleServiceProvider::class];
    }

    /**
     * Run as production, so the config defaults are resolved before the providers register.
     *
     * @param  Application  $app
     */
    protected function resolveApplicationConfiguration($app)
    {
        parent::resolveApplicationConfiguration($app);

        $app->detectEnvironment(fn (): string => 'production');
    }
}
