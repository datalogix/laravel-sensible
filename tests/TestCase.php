<?php

namespace Datalogix\Sensible\Tests;

use Datalogix\Sensible\SensibleServiceProvider;
use GrahamCampbell\TestBench\AbstractPackageTestCase;

abstract class TestCase extends AbstractPackageTestCase
{
    protected static function getServiceProviderClass(): string
    {
        return SensibleServiceProvider::class;
    }
}
