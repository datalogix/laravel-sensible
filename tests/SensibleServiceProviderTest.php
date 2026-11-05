<?php

use Datalogix\Sensible\Sensible;
use Datalogix\Sensible\SensibleManager;
use Datalogix\Sensible\SensibleServiceProvider;
use Datalogix\Sensible\Tests\Fixtures\AppServiceProviderTestCase;
use Datalogix\Sensible\Tests\Fixtures\BootRegisteredConfigurable;
use Datalogix\Sensible\Tests\Fixtures\RegisterRegisteredConfigurable;
use Illuminate\Support\ServiceProvider;
use Mockery\MockInterface;

uses(AppServiceProviderTestCase::class);

test('runs configurables registered in application register', function () {
    expect(RegisterRegisteredConfigurable::$configured)->toBeTrue();
});

test('runs configurables registered in application boot', function () {
    expect(BootRegisteredConfigurable::$configured)->toBeTrue();
});

test('alias and facade resolve the same singleton', function () {
    expect(app('sensible'))->toBe(app(SensibleManager::class))
        ->and(Sensible::getFacadeRoot())->toBe(app(SensibleManager::class));
});

test('runs the manager the facade resolves', function () {
    Sensible::swap(Mockery::mock(SensibleManager::class, function (MockInterface $mock) {
        $mock->shouldReceive('run')->once();
    }));

    (new SensibleServiceProvider($this->app))->boot();
});

test('publishes the config file', function (string $tag) {
    $paths = ServiceProvider::pathsToPublish(SensibleServiceProvider::class, $tag);

    expect(array_map(realpath(...), array_keys($paths)))->toBe([realpath(__DIR__.'/../config/sensible.php')])
        ->and(array_values($paths))->toBe([config_path('sensible.php')]);
})->with(['sensible-config', 'config']);
