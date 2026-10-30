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

test('is disabled by default', function () {
    $fakeSleep = new FakeSleep;

    expect($fakeSleep->enabled())->toBeFalse();
});

test('can be enabled ignored as not during testing', function () {
    config()->set('sensible.'.FakeSleep::class, true);

    $fakeSleep = new FakeSleep;

    expect($fakeSleep->enabled())->toBeFalse();
});

test('can be enabled when during testing', function () {
    config()->set('sensible.'.FakeSleep::class, true);
    app()->detectEnvironment(fn (): string => 'testing');

    $fakeSleep = new FakeSleep;

    expect($fakeSleep->enabled())->toBeTrue();
});
