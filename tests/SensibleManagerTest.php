<?php

use Datalogix\Sensible\Contracts\Configurable;
use Datalogix\Sensible\SensibleManager;
use Datalogix\Sensible\Tests\Fixtures\TestConfigurable;

test('register', function () {
    $mock = $this->mock(TestConfigurable::class);
    $mock->shouldReceive('enabled')->once()->andReturn(true);
    $mock->shouldReceive('configure')->once();

    app()->instance(TestConfigurable::class, $mock);

    $sensibleManager = new SensibleManager;
    $sensibleManager->register(TestConfigurable::class);
    $sensibleManager->run();
});

test('register invalid configurable', function () {
    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage('Foo must implement '.Configurable::class);

    $sensibleManager = new SensibleManager;
    $sensibleManager->register('Foo');
});

test('register ignores duplicates', function () {
    $mock = $this->mock(TestConfigurable::class);
    $mock->shouldReceive('enabled')->once()->andReturn(true);
    $mock->shouldReceive('configure')->once();

    $sensibleManager = new SensibleManager;
    $sensibleManager->register(TestConfigurable::class);
    $sensibleManager->register(TestConfigurable::class);
    $sensibleManager->run();
});

test('register ignores an instance of an already registered class', function () {
    $mock = $this->mock(TestConfigurable::class);
    $mock->shouldReceive('enabled')->once()->andReturn(true);
    $mock->shouldReceive('configure')->once();

    $instance = new TestConfigurable;

    $sensibleManager = new SensibleManager;
    $sensibleManager->register(TestConfigurable::class);
    $sensibleManager->register($instance);
    $sensibleManager->run();

    expect($instance->configured)->toBeFalse();
});

test('register after run applies immediately', function () {
    $sensibleManager = new SensibleManager;
    $sensibleManager->run();

    $mock = $this->mock(TestConfigurable::class);
    $mock->shouldReceive('enabled')->once()->andReturn(true);
    $mock->shouldReceive('configure')->once();

    $sensibleManager->register(TestConfigurable::class);
});

test('register uses the given instance', function () {
    $configurable = new TestConfigurable;

    $sensibleManager = new SensibleManager;
    $sensibleManager->register($configurable);
    $sensibleManager->run();

    expect($configurable->configured)->toBeTrue();
});
