<?php

namespace Datalogix\Sensible\Tests\Configurables;

use Datalogix\Sensible\Configurables\ShouldBeStrict;
use Datalogix\Sensible\Tests\TestCase;
use Illuminate\Database\Eloquent\Model;

class ShouldBeStrictTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Model::shouldBeStrict(false);
    }

    public function test_configure()
    {
        $shouldBeStrict = new ShouldBeStrict;
        $shouldBeStrict->configure();

        $this->assertTrue(Model::preventsLazyLoading());
        $this->assertTrue(Model::preventsSilentlyDiscardingAttributes());
        $this->assertTrue(Model::preventsAccessingMissingAttributes());
    }

    public function test_is_disabled_by_default()
    {
        $shouldBeStrict = new ShouldBeStrict;

        $this->assertFalse($shouldBeStrict->enabled());
    }

    public function test_can_be_enabled()
    {
        config()->set('sensible.'.ShouldBeStrict::class, true);

        $shouldBeStrict = new ShouldBeStrict;

        $this->assertTrue($shouldBeStrict->enabled());
    }
}
