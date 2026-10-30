<?php

use Datalogix\Sensible\Configurables\PreventStrayRequests;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    Http::preventStrayRequests(false);
});

test('configure', function () {
    $preventStrayRequests = new PreventStrayRequests;
    $preventStrayRequests->configure();

    expect(Http::preventingStrayRequests())->toBeTrue();
});

test('is disabled by default', function () {
    $preventStrayRequests = new PreventStrayRequests;

    expect($preventStrayRequests->enabled())->toBeFalse();
});

test('can be enabled ignored as not during testing', function () {
    config()->set('sensible.'.PreventStrayRequests::class, true);

    $preventStrayRequests = new PreventStrayRequests;

    expect($preventStrayRequests->enabled())->toBeFalse();
});

test('can be enabled when during testing', function () {
    config()->set('sensible.'.PreventStrayRequests::class, true);
    app()->detectEnvironment(fn (): string => 'testing');

    $preventStrayRequests = new PreventStrayRequests;

    expect($preventStrayRequests->enabled())->toBeTrue();
});
