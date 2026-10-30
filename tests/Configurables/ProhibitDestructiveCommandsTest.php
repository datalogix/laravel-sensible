<?php

use Datalogix\Sensible\Configurables\ProhibitDestructiveCommands;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    DB::prohibitDestructiveCommands(false);
});

test('configure', function () {
    $prohibitDestructiveCommands = new ProhibitDestructiveCommands;
    $prohibitDestructiveCommands->configure();

    $this->artisan('migrate:fresh')
        ->expectsOutputToContain('prohibited')
        ->assertFailed();
});

test('is enabled by default', function () {
    $prohibitDestructiveCommands = new ProhibitDestructiveCommands;

    expect($prohibitDestructiveCommands->enabled())->toBeTrue();
});

test('is disabled by default outside production', function () {
    app()->detectEnvironment(fn (): string => 'local');

    $prohibitDestructiveCommands = new ProhibitDestructiveCommands;

    expect($prohibitDestructiveCommands->enabled())->toBeFalse();
});

test('can be disabled', function () {
    config()->set('sensible.'.ProhibitDestructiveCommands::class, false);

    $prohibitDestructiveCommands = new ProhibitDestructiveCommands;

    expect($prohibitDestructiveCommands->enabled())->toBeFalse();
});
