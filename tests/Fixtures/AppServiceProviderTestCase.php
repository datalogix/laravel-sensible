<?php

namespace Datalogix\Sensible\Tests\Fixtures;

use Datalogix\Sensible\Tests\TestCase;

abstract class AppServiceProviderTestCase extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return array_merge(parent::getPackageProviders($app), [AppServiceProvider::class]);
    }
}
