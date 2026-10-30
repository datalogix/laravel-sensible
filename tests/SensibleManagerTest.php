<?php

use Datalogix\Sensible\Contracts\Configurable;
use Datalogix\Sensible\SensibleManager;
use Datalogix\Sensible\Tests\Fixtures\TestConfigurable;

test('register', function () {
    $mock = $this->mock(TestConfigurable::class);
    $mock->shouldReceive('enabled')->once()->andReturn(true);
    $mock->shouldReceive('configure')->once();

    $sensibleManager = new SensibleManager;
    $sensibleManager->register(TestConfigurable::class);
    $sensibleManager->run();
});

test('register invalid configurable', function () {
    $sensibleManager = new SensibleManager;
    $sensibleManager->register('Foo');
})->throws(InvalidArgumentException::class, 'Foo must implement '.Configurable::class);

test('register ignores duplicates', function () {
    $mock = $this->mock(TestConfigurable::class);
    $mock->shouldReceive('enabled')->once()->andReturn(true);
    $mock->shouldReceive('configure')->once();

    $sensibleManager = new SensibleManager;
    $sensibleManager->register(TestConfigurable::class);
    $sensibleManager->register(TestConfigurable::class);
    $sensibleManager->run();
});

test('register ignores a duplicate with a leading backslash', function () {
    $mock = $this->mock(TestConfigurable::class);
    $mock->shouldReceive('enabled')->once()->andReturn(true);
    $mock->shouldReceive('configure')->once();

    $sensibleManager = new SensibleManager;
    $sensibleManager->register(TestConfigurable::class);
    $sensibleManager->register('\\'.TestConfigurable::class);
    $sensibleManager->run();
});

test('register replaces an already registered class with the given instance', function () {
    $mock = $this->mock(TestConfigurable::class);
    $mock->shouldNotReceive('enabled');
    $mock->shouldNotReceive('configure');

    $instance = new TestConfigurable;

    $sensibleManager = new SensibleManager;
    $sensibleManager->register(TestConfigurable::class);
    $sensibleManager->register($instance);
    $sensibleManager->run();

    expect($instance->configured)->toBeTrue();
});

test('register ignores a class name already registered as an instance', function () {
    $mock = $this->mock(TestConfigurable::class);
    $mock->shouldNotReceive('enabled');

    $instance = new TestConfigurable;

    $sensibleManager = new SensibleManager;
    $sensibleManager->register($instance);
    $sensibleManager->register(TestConfigurable::class);
    $sensibleManager->run();

    expect($instance->configured)->toBeTrue();
});

test('register an instance after run applies it immediately', function () {
    $instance = new TestConfigurable;

    $sensibleManager = new SensibleManager;
    $sensibleManager->register(TestConfigurable::class);
    $sensibleManager->run();
    $sensibleManager->register($instance);

    expect($instance->configured)->toBeTrue();
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

test('run skips disabled configurables', function () {
    $mock = $this->mock(TestConfigurable::class);
    $mock->shouldReceive('enabled')->once()->andReturn(false);
    $mock->shouldNotReceive('configure');

    $sensibleManager = new SensibleManager;
    $sensibleManager->register(TestConfigurable::class);
    $sensibleManager->run();
});

test('run throws when the container does not resolve a configurable', function () {
    app()->instance(TestConfigurable::class, new stdClass);

    $sensibleManager = new SensibleManager;
    $sensibleManager->register(TestConfigurable::class);
    $sensibleManager->run();
})->throws(InvalidArgumentException::class, TestConfigurable::class.' must resolve to an instance of '.Configurable::class);
