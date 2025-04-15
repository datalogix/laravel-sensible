<?php

namespace Datalogix\Sensible\Tests\Configurables;

use Datalogix\Sensible\Configurables\Unguard;
use Datalogix\Sensible\Tests\TestCase;
use Illuminate\Database\Eloquent\Model;

class UnguardTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Model::reguard();
    }

    public function test_configure()
    {
        $unguard = new Unguard;
        $unguard->configure();

        $this->assertTrue(Model::isUnguarded());
    }

    public function test_is_disabled_by_default()
    {
        $unguard = new Unguard;

        $this->assertFalse($unguard->enabled());
    }

    public function test_can_be_enabled()
    {
        config()->set('sensible.'.Unguard::class, true);

        $unguard = new Unguard;

        $this->assertTrue($unguard->enabled());
    }
}
