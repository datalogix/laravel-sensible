<?php

namespace Datalogix\Sensible\Tests\Configurables;

use Datalogix\Sensible\Contracts\Configurable;
use Datalogix\Sensible\SensibleManager;
use Datalogix\Sensible\Tests\TestCase;

class SensibleManagerTest extends TestCase
{
    public function test_register()
    {
        $mock = $this->mock(TestConfigurable::class);
        $mock->shouldReceive('enabled')->once()->andReturn(true);
        $mock->shouldReceive('configure')->once();

        app()->instance(TestConfigurable::class, $mock);

        $sensibleManager = new SensibleManager;
        $sensibleManager->register(TestConfigurable::class);
        $sensibleManager->run();
    }

    public function test_register_invalid_configurable()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Foo must implement '.Configurable::class);

        $sensibleManager = new SensibleManager;
        $sensibleManager->register('Foo');
    }
}

class TestConfigurable implements Configurable
{
    public function enabled(): bool
    {
        return true;
    }

    public function configure(): void
    {
        //
    }
}
