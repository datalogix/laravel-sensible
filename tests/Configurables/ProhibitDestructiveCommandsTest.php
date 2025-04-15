<?php

namespace Datalogix\Sensible\Tests\Configurables;

use Datalogix\Sensible\Configurables\ProhibitDestructiveCommands;
use Datalogix\Sensible\Tests\TestCase;
use Illuminate\Support\Facades\DB;

class ProhibitDestructiveCommandsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        DB::prohibitDestructiveCommands(false);
    }

    public function test_configure()
    {
        $prohibitDestructiveCommands = new ProhibitDestructiveCommands;
        $prohibitDestructiveCommands->configure();

        $this->artisan('migrate:fresh')->assertFailed();
    }

    public function test_is_enabled_by_default()
    {
        $prohibitDestructiveCommands = new ProhibitDestructiveCommands;

        $this->assertTrue($prohibitDestructiveCommands->enabled());
    }

    public function test_can_be_disabled()
    {
        config()->set('sensible.'.ProhibitDestructiveCommands::class, false);

        $prohibitDestructiveCommands = new ProhibitDestructiveCommands;

        $this->assertFalse($prohibitDestructiveCommands->enabled());
    }
}
