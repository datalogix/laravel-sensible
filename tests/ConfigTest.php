<?php

use Datalogix\Sensible\Configurables\ForceScheme;
use Datalogix\Sensible\Configurables\ProhibitDestructiveCommands;
use Datalogix\Sensible\Configurables\SetDefaultPassword;
use Datalogix\Sensible\Configurables\ShouldBeStrict;
use Illuminate\Container\Container;
use Illuminate\Foundation\Application;

function loadConfigWithAppEnv(?string $appEnv): array
{
    $original = getenv('APP_ENV');

    putenv($appEnv === null ? 'APP_ENV' : "APP_ENV={$appEnv}");

    try {
        return require __DIR__.'/../config/sensible.php';
    } finally {
        putenv($original === false ? 'APP_ENV' : "APP_ENV={$original}");
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

test('environment dependent defaults', function (?string $appEnv, bool $production) {
    $config = loadConfigWithAppEnv($appEnv);

    expect($config[ForceScheme::class])->toBe($production)
        ->and($config[ProhibitDestructiveCommands::class])->toBe($production)
        ->and($config[SetDefaultPassword::class])->toBe($production)
        ->and($config[ShouldBeStrict::class])->toBe(! $production);
})->with([
    'production' => ['production', true],
    'missing' => [null, true],
    'local' => ['local', false],
    'testing' => ['testing', false],
]);
