<?php

namespace Datalogix\Sensible\Tests\Configurables;

use Datalogix\Sensible\Configurables\AggressivePrefetching;
use Datalogix\Sensible\Tests\TestCase;
use Illuminate\Support\Facades\Vite;

class AggressivePrefetchingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Vite::usePrefetchStrategy(null);
    }

    public function test_configure()
    {
        Vite::spy();

        $aggressivePrefetching = new AggressivePrefetching;
        $aggressivePrefetching->configure();

        Vite::shouldHaveReceived('useAggressivePrefetching')->once();
    }

    public function test_is_enabled_by_default()
    {
        $aggressivePrefetching = new AggressivePrefetching;

        $this->assertTrue($aggressivePrefetching->enabled());
    }

    public function test_can_be_disabled()
    {
        config()->set('sensible.'.AggressivePrefetching::class, false);

        $aggressivePrefetching = new AggressivePrefetching;

        $this->assertFalse($aggressivePrefetching->enabled());
    }
}
