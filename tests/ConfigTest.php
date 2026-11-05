<?php

use Datalogix\Sensible\Configurables\AggressivePrefetching;
use Datalogix\Sensible\Configurables\AutomaticallyEagerLoadRelationships;
use Datalogix\Sensible\Configurables\FakeSleep;
use Datalogix\Sensible\Configurables\ForceScheme;
use Datalogix\Sensible\Configurables\ImmutableDates;
use Datalogix\Sensible\Configurables\PreventStrayRequests;
use Datalogix\Sensible\Configurables\ProhibitDestructiveCommands;
use Datalogix\Sensible\Configurables\SetDefaultPassword;
use Datalogix\Sensible\Configurables\ShouldBeStrict;
use Datalogix\Sensible\Configurables\Unguard;
use Datalogix\Sensible\SensibleManager;
use Illuminate\Container\Container;
use Illuminate\Foundation\Application;

function withoutEnv(string $variable, Closure $callback): void
{
    [$putenv, $env, $server] = [getenv($variable), $_ENV[$variable] ?? null, $_SERVER[$variable] ?? null];

    putenv($variable);
    unset($_ENV[$variable], $_SERVER[$variable]);

    try {
        $callback();
    } finally {
        putenv($putenv === false ? $variable : "{$variable}={$putenv}");

        if ($env !== null) {
            $_ENV[$variable] = $env;
        }

        if ($server !== null) {
            $_SERVER[$variable] = $server;
        }
    }
}

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

test('environment dependent defaults are resolved at runtime', function (string $configurable, string $variable) {
    withoutEnv($variable, function () use ($configurable) {
        // Null lets the configurable resolve its default from the detected environment.
        expect((require __DIR__.'/../config/sensible.php')[$configurable])->toBeNull();
    });
})->with([
    [ForceScheme::class, 'SENSIBLE_FORCE_SCHEME'],
    [ProhibitDestructiveCommands::class, 'SENSIBLE_PROHIBIT_DESTRUCTIVE_COMMANDS'],
    [SetDefaultPassword::class, 'SENSIBLE_SET_DEFAULT_PASSWORD'],
    [ShouldBeStrict::class, 'SENSIBLE_SHOULD_BE_STRICT'],
]);

test('environment dependent entries read their env variable', function (string $configurable, string $variable) {
    withoutEnv($variable, function () use ($configurable, $variable) {
        expect((require __DIR__.'/../config/sensible.php')[$configurable])->toBeNull();

        putenv("{$variable}=false");
        expect((require __DIR__.'/../config/sensible.php')[$configurable])->toBeFalse();
    });
})->with([
    [ForceScheme::class, 'SENSIBLE_FORCE_SCHEME'],
    [ProhibitDestructiveCommands::class, 'SENSIBLE_PROHIBIT_DESTRUCTIVE_COMMANDS'],
    [SetDefaultPassword::class, 'SENSIBLE_SET_DEFAULT_PASSWORD'],
    [ShouldBeStrict::class, 'SENSIBLE_SHOULD_BE_STRICT'],
]);

test('has an entry for every built-in configurable', function () {
    $configurables = (new ReflectionProperty(SensibleManager::class, 'configurables'))->getDefaultValue();

    expect(array_keys(require __DIR__.'/../config/sensible.php'))->toEqualCanonicalizing($configurables);
});

test('entries read their env variable', function (string $configurable, string $variable) {
    withoutEnv($variable, function () use ($configurable, $variable) {
        putenv("{$variable}=off");
        expect((require __DIR__.'/../config/sensible.php')[$configurable])->toBe('off');

        putenv("{$variable}=");
        expect((require __DIR__.'/../config/sensible.php')[$configurable])->toBe('');
    });
})->with([
    [AggressivePrefetching::class, 'SENSIBLE_AGGRESSIVE_PREFETCHING'],
    [AutomaticallyEagerLoadRelationships::class, 'SENSIBLE_AUTOMATICALLY_EAGER_LOAD_RELATIONSHIPS'],
    [FakeSleep::class, 'SENSIBLE_FAKE_SLEEP'],
    [ForceScheme::class, 'SENSIBLE_FORCE_SCHEME'],
    [ImmutableDates::class, 'SENSIBLE_IMMUTABLE_DATES'],
    [PreventStrayRequests::class, 'SENSIBLE_PREVENT_STRAY_REQUESTS'],
    [ProhibitDestructiveCommands::class, 'SENSIBLE_PROHIBIT_DESTRUCTIVE_COMMANDS'],
    [SetDefaultPassword::class, 'SENSIBLE_SET_DEFAULT_PASSWORD'],
    [ShouldBeStrict::class, 'SENSIBLE_SHOULD_BE_STRICT'],
    [Unguard::class, 'SENSIBLE_UNGUARD'],
]);

test('test-only configurables are on by default', function (string $configurable, string $variable) {
    withoutEnv($variable, function () use ($configurable) {
        expect((require __DIR__.'/../config/sensible.php')[$configurable])->toBeTrue();
    });
})->with([
    [FakeSleep::class, 'SENSIBLE_FAKE_SLEEP'],
    [PreventStrayRequests::class, 'SENSIBLE_PREVENT_STRAY_REQUESTS'],
]);
