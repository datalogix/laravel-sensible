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

    public function test_configure_simple()
    {
        $this->markTestSkippedUnless(
            method_exists(Password::class, 'appliedRules'),
            'The appliedRules method is not available in this version of Laravel.'
        );

        config()->set('sensible.'.SetDefaultPassword::class, 'simple');

        $setDefaultPassword = new SetDefaultPassword;
        $setDefaultPassword->configure();

        $passwordRules = Password::default()->appliedRules();

        $this->assertEquals($passwordRules['min'], 6);
        $this->assertFalse($passwordRules['mixedCase']);
        $this->assertFalse($passwordRules['letters']);
        $this->assertFalse($passwordRules['numbers']);
        $this->assertFalse($passwordRules['symbols']);
        $this->assertFalse($passwordRules['uncompromised']);
    }

    public function test_configure_numeric()
    {
        $this->markTestSkippedUnless(
            method_exists(Password::class, 'appliedRules'),
            'The appliedRules method is not available in this version of Laravel.'
        );

        config()->set('sensible.'.SetDefaultPassword::class, 'numeric');

        $setDefaultPassword = new SetDefaultPassword;
        $setDefaultPassword->configure();

        $passwordRules = Password::default()->appliedRules();

        $this->assertEquals($passwordRules['min'], 4);
        $this->assertEquals($passwordRules['max'], 6);
        $this->assertTrue($passwordRules['numbers']);
    }

    public function test_configure_pin()
    {
        $this->markTestSkippedUnless(
            method_exists(Password::class, 'appliedRules'),
            'The appliedRules method is not available in this version of Laravel.'
        );

        config()->set('sensible.'.SetDefaultPassword::class, 'pin');

        $setDefaultPassword = new SetDefaultPassword;
        $setDefaultPassword->configure();

        $passwordRules = Password::default()->appliedRules();

        $this->assertEquals($passwordRules['min'], 4);
        $this->assertEquals($passwordRules['max'], 4);
        $this->assertTrue($passwordRules['numbers']);
    }

    public function test_configure_passphrase()
    {
        $this->markTestSkippedUnless(
            method_exists(Password::class, 'appliedRules'),
            'The appliedRules method is not available in this version of Laravel.'
        );

        config()->set('sensible.'.SetDefaultPassword::class, 'passphrase');

        $setDefaultPassword = new SetDefaultPassword;
        $setDefaultPassword->configure();

        $passwordRules = Password::default()->appliedRules();

        $this->assertEquals($passwordRules['min'], 16);
        $this->assertFalse($passwordRules['mixedCase']);
        $this->assertFalse($passwordRules['letters']);
        $this->assertFalse($passwordRules['numbers']);
        $this->assertFalse($passwordRules['symbols']);
        $this->assertFalse($passwordRules['uncompromised']);
    }

    public function test_configure_alphanumeric()
    {
        $this->markTestSkippedUnless(
            method_exists(Password::class, 'appliedRules'),
            'The appliedRules method is not available in this version of Laravel.'
        );

        config()->set('sensible.'.SetDefaultPassword::class, 'alphanumeric');

        $setDefaultPassword = new SetDefaultPassword;
        $setDefaultPassword->configure();

        $passwordRules = Password::default()->appliedRules();

        $this->assertEquals($passwordRules['min'], 8);
        $this->assertEquals($passwordRules['max'], 20);
        $this->assertTrue($passwordRules['letters']);
        $this->assertTrue($passwordRules['numbers']);
        $this->assertFalse($passwordRules['mixedCase']);
        $this->assertFalse($passwordRules['symbols']);
        $this->assertFalse($passwordRules['uncompromised']);
    }

    public function test_is_enabled_by_default()
    {
        $setDefaultPassword = new SetDefaultPassword;

        $this->assertTrue($setDefaultPassword->enabled());
    }

    public function test_is_enabled_when_a_type_is_configured()
    {
        config()->set('sensible.'.SetDefaultPassword::class, 'simple');

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
