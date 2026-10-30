<?php

use Datalogix\Sensible\Configurables\ForceScheme;
use Illuminate\Support\Facades\URL;

beforeEach(function () {
    URL::forceScheme(null);
});

test('configure forces https in production', function () {
    $forceScheme = new ForceScheme;
    $forceScheme->configure();

    expect(URL::to('/test'))->toStartWith('https://');
});

test('configure forces https outside production when enabled', function () {
    app()->detectEnvironment(fn (): string => 'local');

    $forceScheme = new ForceScheme;
    $forceScheme->configure();

    expect(URL::to('/test'))->toStartWith('https://');
});

test('is enabled by default', function () {
    $forceScheme = new ForceScheme;

    expect($forceScheme->enabled())->toBeTrue();
});

test('can be disabled', function () {
    config()->set('sensible.'.ForceScheme::class, false);

    $forceScheme = new ForceScheme;

    expect($forceScheme->enabled())->toBeFalse();
});

test('accepts boolean like strings', function () {
    $forceScheme = new ForceScheme;

    foreach (['1', 'on', 'yes', 'true'] as $value) {
        config()->set('sensible.'.ForceScheme::class, $value);
        expect($forceScheme->enabled())->toBeTrue($value);
    }

    foreach (['0', 'off', 'no', 'false', ''] as $value) {
        config()->set('sensible.'.ForceScheme::class, $value);
        expect($forceScheme->enabled())->toBeFalse($value);
    }
});

test('throws on invalid value', function () {
    config()->set('sensible.'.ForceScheme::class, 'banana');

    $this->expectException(InvalidArgumentException::class);

    (new ForceScheme)->enabled();
});
