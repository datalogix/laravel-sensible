<?php

use Datalogix\Sensible\Configurables\ForceScheme;
use Illuminate\Support\Facades\URL;

beforeEach(function () {
    URL::forceScheme(null);
});

test('configure forces https', function () {
    $forceScheme = new ForceScheme;
    $forceScheme->configure();

    expect(URL::to('/test'))->toStartWith('https://');
});

test('is enabled by default', function () {
    $forceScheme = new ForceScheme;

    expect($forceScheme->enabled())->toBeTrue();
});

test('is disabled by default outside production', function () {
    app()->detectEnvironment(fn (): string => 'local');

    $forceScheme = new ForceScheme;

    expect($forceScheme->enabled())->toBeFalse();
});

test('can be enabled outside production', function () {
    config()->set('sensible.'.ForceScheme::class, true);
    app()->detectEnvironment(fn (): string => 'local');

    $forceScheme = new ForceScheme;

    expect($forceScheme->enabled())->toBeTrue();
});

test('can be disabled', function () {
    config()->set('sensible.'.ForceScheme::class, false);

    $forceScheme = new ForceScheme;

    expect($forceScheme->enabled())->toBeFalse();
});

test('accepts boolean like strings', function (string $value, bool $expected) {
    config()->set('sensible.'.ForceScheme::class, $value);

    expect((new ForceScheme)->enabled())->toBe($expected);
})->with([
    ['1', true],
    ['on', true],
    ['yes', true],
    ['true', true],
    ['0', false],
    ['off', false],
    ['no', false],
    ['false', false],
    ['', false],
]);

test('throws on invalid value', function () {
    config()->set('sensible.'.ForceScheme::class, 'banana');

    (new ForceScheme)->enabled();
})->throws(InvalidArgumentException::class);
