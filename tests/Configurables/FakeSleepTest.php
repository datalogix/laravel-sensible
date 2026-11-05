<?php

use Datalogix\Sensible\Configurables\FakeSleep;
use Illuminate\Support\Sleep;

beforeEach(function () {
    Sleep::fake(false);
});

test('configure', function () {
    $fakeSleep = new FakeSleep;
    $fakeSleep->configure();

    Sleep::usleep(1);
    Sleep::assertSleptTimes(1);
});

test('is disabled outside unit tests by default', function () {
    $fakeSleep = new FakeSleep;

    expect($fakeSleep->enabled())->toBeFalse();
});

test('stays disabled outside unit tests', function () {
    config()->set('sensible.'.FakeSleep::class, true);

    $fakeSleep = new FakeSleep;

    expect($fakeSleep->enabled())->toBeFalse();
});

test('is enabled when running unit tests', function () {
    config()->set('sensible.'.FakeSleep::class, true);
    app()->detectEnvironment(fn (): string => 'testing');

    $fakeSleep = new FakeSleep;

    expect($fakeSleep->enabled())->toBeTrue();
});

test('can be disabled when running unit tests', function () {
    config()->set('sensible.'.FakeSleep::class, false);
    app()->detectEnvironment(fn (): string => 'testing');

    expect((new FakeSleep)->enabled())->toBeFalse();
});
