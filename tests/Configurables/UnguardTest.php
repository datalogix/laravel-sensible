<?php

use Datalogix\Sensible\Configurables\Unguard;
use Illuminate\Database\Eloquent\Model;

beforeEach(function () {
    Model::reguard();
});

test('configure', function () {
    $unguard = new Unguard;
    $unguard->configure();

    expect(Model::isUnguarded())->toBeTrue();
});

test('is disabled by default', function () {
    $unguard = new Unguard;

    expect($unguard->enabled())->toBeFalse();
});

test('can be enabled', function () {
    config()->set('sensible.'.Unguard::class, true);

    $unguard = new Unguard;

    expect($unguard->enabled())->toBeTrue();
});
