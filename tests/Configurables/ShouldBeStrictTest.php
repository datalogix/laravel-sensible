<?php

use Datalogix\Sensible\Configurables\ShouldBeStrict;
use Illuminate\Database\Eloquent\Model;

beforeEach(function () {
    Model::shouldBeStrict(false);
});

test('configure', function () {
    $shouldBeStrict = new ShouldBeStrict;
    $shouldBeStrict->configure();

    expect(Model::preventsLazyLoading())->toBeTrue();
    expect(Model::preventsSilentlyDiscardingAttributes())->toBeTrue();
    expect(Model::preventsAccessingMissingAttributes())->toBeTrue();
});

test('is disabled by default', function () {
    $shouldBeStrict = new ShouldBeStrict;

    expect($shouldBeStrict->enabled())->toBeFalse();
});

test('is enabled by default outside production', function () {
    app()->detectEnvironment(fn (): string => 'local');

    $shouldBeStrict = new ShouldBeStrict;

    expect($shouldBeStrict->enabled())->toBeTrue();
});

test('can be enabled', function () {
    config()->set('sensible.'.ShouldBeStrict::class, true);

    $shouldBeStrict = new ShouldBeStrict;

    expect($shouldBeStrict->enabled())->toBeTrue();
});
