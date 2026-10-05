<?php

namespace Datalogix\Sensible\Tests;

use Datalogix\Sensible\Contracts\Configurable;
use Datalogix\Sensible\SensibleManager;

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

    public function test_register_ignores_duplicates()
    {
        $mock = $this->mock(TestConfigurable::class);
        $mock->shouldReceive('enabled')->once()->andReturn(true);
        $mock->shouldReceive('configure')->once();

        $sensibleManager = new SensibleManager;
        $sensibleManager->register(TestConfigurable::class);
        $sensibleManager->register(TestConfigurable::class);
        $sensibleManager->run();
    }

    public function test_register_ignores_an_instance_of_an_already_registered_class()
    {
        $mock = $this->mock(TestConfigurable::class);
        $mock->shouldReceive('enabled')->once()->andReturn(true);
        $mock->shouldReceive('configure')->once();

        $instance = new TestConfigurable;

        $sensibleManager = new SensibleManager;
        $sensibleManager->register(TestConfigurable::class);
        $sensibleManager->register($instance);
        $sensibleManager->run();

        $this->assertFalse($instance->configured);
    }

    public function test_register_after_run_applies_immediately()
    {
        $sensibleManager = new SensibleManager;
        $sensibleManager->run();

        $mock = $this->mock(TestConfigurable::class);
        $mock->shouldReceive('enabled')->once()->andReturn(true);
        $mock->shouldReceive('configure')->once();

        $sensibleManager->register(TestConfigurable::class);
    }

    public function test_register_uses_the_given_instance()
    {
        $configurable = new TestConfigurable;

        $sensibleManager = new SensibleManager;
        $sensibleManager->register($configurable);
        $sensibleManager->run();

        $this->assertTrue($configurable->configured);
    }
}

class TestConfigurable implements Configurable
{
    public bool $configured = false;

    public function enabled(): bool
    {
        return true;
    }

    public function configure(): void
    {
        $this->configured = true;
    }
}
