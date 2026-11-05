<?php

use Datalogix\Sensible\Tests\Fixtures\ReadsConfigurationConfigurable;

test('config key uses the configurable class name', function () {
    expect((new ReadsConfigurationConfigurable)->key())
        ->toBe('sensible.'.ReadsConfigurationConfigurable::class);
});

test('is disabled when the config value is missing', function () {
    expect((new ReadsConfigurationConfigurable)->isEnabled())->toBeFalse();
});

test('is disabled when the config value is null', function () {
    config()->set('sensible.'.ReadsConfigurationConfigurable::class, null);

    expect((new ReadsConfigurationConfigurable)->isEnabled())->toBeFalse();
});

test('uses the default when the config value is not set', function () {
    expect((new ReadsConfigurationConfigurable)->isEnabled(default: true))->toBeTrue();
});

test('ignores the default when the config value is set', function () {
    config()->set('sensible.'.ReadsConfigurationConfigurable::class, false);

    expect((new ReadsConfigurationConfigurable)->isEnabled(default: true))->toBeFalse();
});

test('uses the default when the config value is null', function () {
    config()->set('sensible.'.ReadsConfigurationConfigurable::class, null);

    expect((new ReadsConfigurationConfigurable)->isEnabled(default: true))->toBeTrue();
});

test('string values override the default', function (string $value) {
    config()->set('sensible.'.ReadsConfigurationConfigurable::class, $value);

    expect((new ReadsConfigurationConfigurable)->isEnabled(default: true))->toBeFalse();
})->with(['0', 'off', 'no', 'false']);

test('still throws on invalid values when a default is given', function () {
    config()->set('sensible.'.ReadsConfigurationConfigurable::class, 'banana');

    expect(fn () => (new ReadsConfigurationConfigurable)->isEnabled(default: true))
        ->toThrow(InvalidArgumentException::class);
});

test('accepts boolean-like strings regardless of case', function (string $value) {
    config()->set('sensible.'.ReadsConfigurationConfigurable::class, $value);

    expect((new ReadsConfigurationConfigurable)->isEnabled())->toBeTrue();
})->with(['1', 'on', 'yes', 'true', 'ON', 'True']);

test('an empty string overrides the default', function () {
    config()->set('sensible.'.ReadsConfigurationConfigurable::class, '');

    expect((new ReadsConfigurationConfigurable)->isEnabled(default: true))->toBeFalse();
});

test('throws on non-scalar values', function () {
    config()->set('sensible.'.ReadsConfigurationConfigurable::class, ['on']);

    expect(fn () => (new ReadsConfigurationConfigurable)->isEnabled())
        ->toThrow(InvalidArgumentException::class);
});

test('accepts native booleans and integers', function (mixed $value, bool $expected) {
    config()->set('sensible.'.ReadsConfigurationConfigurable::class, $value);

    expect((new ReadsConfigurationConfigurable)->isEnabled())->toBe($expected);
})->with([
    [true, true],
    [false, false],
    [1, true],
    [0, false],
]);

test('throws with the key and the given value', function (mixed $value, string $exported) {
    config()->set('sensible.'.ReadsConfigurationConfigurable::class, $value);

    expect(fn () => (new ReadsConfigurationConfigurable)->isEnabled())->toThrow(
        InvalidArgumentException::class,
        sprintf(
            'Configuration value for key [sensible.%s] must be a boolean, [%s] given.',
            ReadsConfigurationConfigurable::class,
            $exported,
        ),
    );
})->with([
    [2, '2'],
    ['banana', "'banana'"],
]);
