<?php

namespace Datalogix\Sensible\Tests\Configurables;

use Datalogix\Sensible\Configurables\FakeSleep;
use Datalogix\Sensible\Tests\TestCase;
use Illuminate\Support\Sleep;

class FakeSleepTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Sleep::fake(false);
    }

    public function test_configure()
    {
        $fakeSleep = new FakeSleep;
        $fakeSleep->configure();

        Sleep::usleep(1);
        Sleep::assertSleptTimes(1);
    }

    public function test_is_disabled_by_default()
    {
        $fakeSleep = new FakeSleep;

        $this->assertFalse($fakeSleep->enabled());
    }

    public function test_can_be_enabled_ignored_as_not_during_testing()
    {
        config()->set('sensible.'.FakeSleep::class, true);

        $fakeSleep = new FakeSleep;

        $this->assertFalse($fakeSleep->enabled());
    }

    public function test_can_be_enabled_when_during_testing()
    {
        config()->set('sensible.'.FakeSleep::class, true);
        app()->detectEnvironment(fn (): string => 'testing');

        $fakeSleep = new FakeSleep;

        $this->assertTrue($fakeSleep->enabled());
    }
}
