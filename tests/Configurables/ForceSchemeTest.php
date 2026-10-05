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

    public function test_configure_forces_https_in_production()
    {
        $forceScheme = new ForceScheme;
        $forceScheme->configure();

        $this->assertStringStartsWith('https://', URL::to('/test'));
    }

    public function test_configure_forces_https_outside_production_when_enabled()
    {
        app()->detectEnvironment(fn (): string => 'local');

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

    public function test_accepts_boolean_like_strings()
    {
        $forceScheme = new ForceScheme;

        foreach (['1', 'on', 'yes', 'true'] as $value) {
            config()->set('sensible.'.ForceScheme::class, $value);
            $this->assertTrue($forceScheme->enabled(), $value);
        }

        foreach (['0', 'off', 'no', 'false', ''] as $value) {
            config()->set('sensible.'.ForceScheme::class, $value);
            $this->assertFalse($forceScheme->enabled(), $value);
        }
    }

    public function test_throws_on_invalid_value()
    {
        config()->set('sensible.'.ForceScheme::class, 'banana');

        $this->expectException(\InvalidArgumentException::class);

        (new ForceScheme)->enabled();
    }
}
