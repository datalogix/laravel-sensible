<?php

namespace Datalogix\Sensible\Tests\Configurables;

use Datalogix\Sensible\Configurables\ForceScheme;
use Datalogix\Sensible\Tests\TestCase;
use Illuminate\Support\Facades\URL;

class ForceSchemeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        URL::forceScheme(null);
    }

    public function test_configure()
    {
        $forceScheme = new ForceScheme;
        $forceScheme->configure();

        $this->assertStringStartsWith('https://', URL::to('/test'));
    }

    public function test_is_enabled_by_default()
    {
        $forceScheme = new ForceScheme;

        $this->assertTrue($forceScheme->enabled());
    }

    public function test_can_be_disabled()
    {
        config()->set('sensible.'.ForceScheme::class, false);

        $forceScheme = new ForceScheme;

        $this->assertFalse($forceScheme->enabled());
    }
}
