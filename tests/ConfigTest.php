<?php

use Datalogix\Sensible\Configurables\ForceScheme;
use Datalogix\Sensible\Configurables\ProhibitDestructiveCommands;
use Datalogix\Sensible\Configurables\SetDefaultPassword;
use Datalogix\Sensible\Configurables\ShouldBeStrict;
use Illuminate\Container\Container;
use Illuminate\Foundation\Application;

test('loads before the environment is detected', function () {
    $container = Container::getInstance();

    // A published config file is loaded before the application environment is detected.
    new Application(dirname(__DIR__));

    try {
        expect(require __DIR__.'/../config/sensible.php')->toBeArray();
    } finally {
        Container::setInstance($container);
    }
});

test('environment dependent defaults are resolved at runtime', function (string $configurable) {
    // Null lets the configurable resolve its default from the detected environment.
    expect((require __DIR__.'/../config/sensible.php')[$configurable])->toBeNull();
})->with([
    ForceScheme::class,
    ProhibitDestructiveCommands::class,
    SetDefaultPassword::class,
    ShouldBeStrict::class,
]);

test('environment dependent entries read their env variable', function (string $configurable, string $variable) {
    $original = getenv($variable);

    try {
        putenv($variable);
        expect((require __DIR__.'/../config/sensible.php')[$configurable])->toBeNull();

        putenv("{$variable}=false");
        expect((require __DIR__.'/../config/sensible.php')[$configurable])->toBeFalse();
    } finally {
        putenv($original === false ? $variable : "{$variable}={$original}");
    }
})->with([
    [ForceScheme::class, 'SENSIBLE_FORCE_SCHEME'],
    [ProhibitDestructiveCommands::class, 'SENSIBLE_PROHIBIT_DESTRUCTIVE_COMMANDS'],
    [SetDefaultPassword::class, 'SENSIBLE_SET_DEFAULT_PASSWORD'],
    [ShouldBeStrict::class, 'SENSIBLE_SHOULD_BE_STRICT'],
]);
