<?php

use Carbon\Carbon;
use Datalogix\Sensible\Configurables\ImmutableDates;
use Illuminate\Support\Facades\Date;

beforeEach(function () {
    Date::use(Carbon::class);
});

test('configure', function () {
    $immutableDates = new ImmutableDates;
    $immutableDates->configure();

    expect(now()->isImmutable())->toBeTrue();
});

test('is enabled by default', function () {
    $immutableDates = new ImmutableDates;

    expect($immutableDates->enabled())->toBeTrue();
});

test('can be disabled', function () {
    config()->set('sensible.'.ImmutableDates::class, false);

    $immutableDates = new ImmutableDates;

    expect($immutableDates->enabled())->toBeFalse();
});
