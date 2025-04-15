<?php

namespace Datalogix\Sensible\Tests\Configurables;

use Datalogix\Sensible\Configurables\SetDefaultPassword;
use Datalogix\Sensible\Tests\TestCase;
use Illuminate\Validation\Rules\Password;

class SetDefaultPasswordTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Password::defaults(null);
    }

    public function test_configure()
    {
        $this->markTestSkippedUnless(
            method_exists(Password::class, 'appliedRules'),
            'The appliedRules method is not available in this version of Laravel.'
        );

        $setDefaultPassword = new SetDefaultPassword;
        $setDefaultPassword->configure();

        $passwordRules = Password::default()->appliedRules();

        $this->assertEquals($passwordRules['min'], 8);
        $this->assertEquals($passwordRules['max'], 20);
        $this->assertTrue($passwordRules['mixedCase']);
        $this->assertTrue($passwordRules['letters']);
        $this->assertTrue($passwordRules['numbers']);
        $this->assertTrue($passwordRules['symbols']);
        $this->assertTrue($passwordRules['uncompromised']);
    }

    public function test_is_enabled_by_default()
    {
        $setDefaultPassword = new SetDefaultPassword;

        $this->assertTrue($setDefaultPassword->enabled());
    }

    public function test_can_be_disabled()
    {
        config()->set('sensible.'.SetDefaultPassword::class, false);

        $setDefaultPassword = new SetDefaultPassword;

        $this->assertFalse($setDefaultPassword->enabled());
    }
}
