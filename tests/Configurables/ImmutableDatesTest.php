<?php

namespace Datalogix\Sensible\Tests\Configurables;

use Carbon\Carbon;
use Datalogix\Sensible\Configurables\ImmutableDates;
use Datalogix\Sensible\Tests\TestCase;
use Illuminate\Support\Facades\Date;

class ImmutableDatesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Date::use(Carbon::class);
    }

    public function test_configure()
    {
        $immutableDates = new ImmutableDates;
        $immutableDates->configure();

        $this->assertTrue(now()->isImmutable());
    }

    public function test_is_enabled_by_default()
    {
        $immutableDates = new ImmutableDates;

        $this->assertTrue($immutableDates->enabled());
    }

    public function test_can_be_disabled()
    {
        config()->set('sensible.'.ImmutableDates::class, false);

        $immutableDates = new ImmutableDates;

        $this->assertFalse($immutableDates->enabled());
    }
}
