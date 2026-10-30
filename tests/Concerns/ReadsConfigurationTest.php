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
