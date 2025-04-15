<?php

namespace Datalogix\Sensible\Tests\Configurables;

use Datalogix\Sensible\Configurables\PreventStrayRequests;
use Datalogix\Sensible\Tests\TestCase;
use Illuminate\Support\Facades\Http;

class PreventStrayRequestsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Http::preventStrayRequests(false);
    }

    public function test_configure()
    {
        $preventStrayRequests = new PreventStrayRequests;
        $preventStrayRequests->configure();

        $this->assertTrue(Http::preventingStrayRequests());
    }

    public function test_is_disabled_by_default()
    {
        $preventStrayRequests = new PreventStrayRequests;

        $this->assertFalse($preventStrayRequests->enabled());
    }

    public function test_can_be_enabled_ignored_as_not_during_testing()
    {
        config()->set('sensible.'.PreventStrayRequests::class, true);

        $preventStrayRequests = new PreventStrayRequests;

        $this->assertFalse($preventStrayRequests->enabled());
    }

    public function test_can_be_enabled_when_during_testing()
    {
        config()->set('sensible.'.PreventStrayRequests::class, true);
        app()->detectEnvironment(fn (): string => 'testing');

        $preventStrayRequests = new PreventStrayRequests;

        $this->assertTrue($preventStrayRequests->enabled());
    }
}
