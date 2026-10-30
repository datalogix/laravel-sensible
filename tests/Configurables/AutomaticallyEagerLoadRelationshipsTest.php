<?php

use Datalogix\Sensible\Configurables\AutomaticallyEagerLoadRelationships;
use Illuminate\Database\Eloquent\Model;

beforeEach(function () {
    $this->markTestSkippedUnless(
        method_exists(Model::class, 'automaticallyEagerLoadRelationships'),
        'Automatically eager loading relationships is not supported in this version of Laravel.'
    );
});

test('configure', function () {
    Model::automaticallyEagerLoadRelationships(false);

    $automaticallyEagerLoadRelationships = new AutomaticallyEagerLoadRelationships;
    $automaticallyEagerLoadRelationships->configure();

    expect(Model::isAutomaticallyEagerLoadingRelationships())->toBeTrue();
});

test('is enabled by default', function () {
    $automaticallyEagerLoadRelationships = new AutomaticallyEagerLoadRelationships;

    expect($automaticallyEagerLoadRelationships->enabled())->toBeTrue();
});

test('can be disabled', function () {
    config()->set('sensible.'.AutomaticallyEagerLoadRelationships::class, false);

    $automaticallyEagerLoadRelationships = new AutomaticallyEagerLoadRelationships;

    expect($automaticallyEagerLoadRelationships->enabled())->toBeFalse();
});
