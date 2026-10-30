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

test('is disabled outside unit tests by default', function () {
    $preventStrayRequests = new PreventStrayRequests;

    expect($preventStrayRequests->enabled())->toBeFalse();
});

test('stays disabled outside unit tests', function () {
    config()->set('sensible.'.PreventStrayRequests::class, true);

    $preventStrayRequests = new PreventStrayRequests;

    expect($preventStrayRequests->enabled())->toBeFalse();
});

test('is enabled when running unit tests', function () {
    config()->set('sensible.'.PreventStrayRequests::class, true);
    app()->detectEnvironment(fn (): string => 'testing');

    $preventStrayRequests = new PreventStrayRequests;

    expect($preventStrayRequests->enabled())->toBeTrue();
});
