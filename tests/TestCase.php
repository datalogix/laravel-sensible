<?php

namespace Datalogix\Sensible\Tests;

use Datalogix\Sensible\SensibleServiceProvider;
use GrahamCampbell\TestBench\AbstractPackageTestCase;
use Illuminate\Foundation\Application;

abstract class TestCase extends AbstractPackageTestCase
{
    protected static function getServiceProviderClass(): string
    {
        return SensibleServiceProvider::class;
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
