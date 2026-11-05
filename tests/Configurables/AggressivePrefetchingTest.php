<?php

use Datalogix\Sensible\Configurables\AggressivePrefetching;
use Illuminate\Support\Facades\Vite;

beforeEach(function () {
    Vite::usePrefetchStrategy(null);
});

test('configure', function () {
    Vite::spy();

    $aggressivePrefetching = new AggressivePrefetching;
    $aggressivePrefetching->configure();

    Vite::shouldHaveReceived('useAggressivePrefetching')->once();
});

test('is enabled by default', function () {
    $aggressivePrefetching = new AggressivePrefetching;

    expect($aggressivePrefetching->enabled())->toBeTrue();
});

test('can be disabled', function () {
    config()->set('sensible.'.AggressivePrefetching::class, false);

    $aggressivePrefetching = new AggressivePrefetching;

    expect($aggressivePrefetching->enabled())->toBeFalse();
});
