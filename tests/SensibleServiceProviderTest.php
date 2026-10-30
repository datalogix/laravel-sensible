<?php

use Datalogix\Sensible\Tests\Fixtures\AppServiceProviderTestCase;
use Datalogix\Sensible\Tests\Fixtures\BootRegisteredConfigurable;

uses(AppServiceProviderTestCase::class);

test('runs configurables registered in application boot', function () {
    expect(BootRegisteredConfigurable::$configured)->toBeTrue();
});
