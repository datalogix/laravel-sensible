<?php

namespace Datalogix\Sensible\Tests\Configurables;

use Datalogix\Sensible\Configurables\AutomaticallyEagerLoadRelationships;
use Datalogix\Sensible\Tests\TestCase;
use Illuminate\Database\Eloquent\Model;

class AutomaticallyEagerLoadRelationshipsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->markTestSkippedUnless(
            method_exists(Model::class, 'automaticallyEagerLoadRelationships'),
            'Automatically eager loading relationships is not supported in this version of Laravel.'
        );
    }

    public function test_configure()
    {
        Model::automaticallyEagerLoadRelationships(false);

        $automaticallyEagerLoadRelationships = new AutomaticallyEagerLoadRelationships;
        $automaticallyEagerLoadRelationships->configure();

        $this->assertTrue(Model::isAutomaticallyEagerLoadingRelationships());
    }

    public function test_is_enabled_by_default()
    {
        $automaticallyEagerLoadRelationships = new AutomaticallyEagerLoadRelationships;

        $this->assertTrue($automaticallyEagerLoadRelationships->enabled());
    }

    public function test_can_be_disabled()
    {
        config()->set('sensible.'.AutomaticallyEagerLoadRelationships::class, false);

        $automaticallyEagerLoadRelationships = new AutomaticallyEagerLoadRelationships;

        $this->assertFalse($automaticallyEagerLoadRelationships->enabled());
    }
}
