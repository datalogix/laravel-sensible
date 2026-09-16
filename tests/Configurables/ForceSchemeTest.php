<?php

namespace Datalogix\Sensible\Tests\Configurables;

use Datalogix\Sensible\Configurables\ForceScheme;
use Datalogix\Sensible\Tests\TestCase;
use Illuminate\Contracts\Routing\UrlGenerator;
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

    public function test_configure_does_not_force_https_outside_production()
    {
        app()->detectEnvironment(fn (): string => 'local');

        $forceScheme = new ForceScheme;
        $forceScheme->configure();

        $this->assertStringStartsWith('http://', URL::to('/test'));
    }

    public function test_configure_falls_back_to_force_scheme_when_force_https_is_unavailable()
    {
        $urlGenerator = new class implements UrlGenerator
        {
            public ?string $forcedScheme = null;

            public function forceScheme($scheme)
            {
                $this->forcedScheme = $scheme;
            }

            public function current() {}

            public function previous($fallback = false) {}

            public function to($path, $extra = [], $secure = null) {}

            public function secure($path, $parameters = []) {}

            public function asset($path, $secure = null) {}

            public function route($name, $parameters = [], $absolute = true) {}

            public function signedRoute($name, $parameters = [], $expiration = null, $absolute = true) {}

            public function temporarySignedRoute($name, $expiration, $parameters = [], $absolute = true) {}

            public function query($path, $query = [], $extra = [], $secure = null) {}

            public function action($action, $parameters = [], $absolute = true) {}

            public function getRootControllerNamespace() {}

            public function setRootControllerNamespace($rootNamespace) {}
        };

        URL::swap($urlGenerator);

        $forceScheme = new ForceScheme;
        $forceScheme->configure();

        $this->assertSame('https', $urlGenerator->forcedScheme);
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
