<?php

namespace Datalogix\Sensible\Tests\Configurables;

use Datalogix\Sensible\Configurables\SetDefaultPassword;
use Datalogix\Sensible\Enums\PasswordType;
use Datalogix\Sensible\Tests\TestCase;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class SetDefaultPasswordTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Password::$defaultCallback = null;
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
        $this->assertNull($passwordRules['max']);
        $this->assertTrue($passwordRules['mixedCase']);
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
        $this->assertSame(['digits_between:4,6'], $passwordRules['customRules']);
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
        $this->assertSame(['digits:4'], $passwordRules['customRules']);
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
        $this->assertNull($passwordRules['max']);
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

    public function test_pin_rejects_non_digits()
    {
        config()->set('sensible.'.SetDefaultPassword::class, 'pin');

        (new SetDefaultPassword)->configure();

        $this->assertTrue(Validator::make(['password' => '1234'], ['password' => Password::default()])->passes());
        $this->assertTrue(Validator::make(['password' => 'abc1'], ['password' => Password::default()])->fails());
    }

    public function test_accepts_boolean_like_strings()
    {
        $setDefaultPassword = new SetDefaultPassword;

        config()->set('sensible.'.SetDefaultPassword::class, '1');
        $this->assertTrue($setDefaultPassword->enabled());

        config()->set('sensible.'.SetDefaultPassword::class, 'off');
        $this->assertFalse($setDefaultPassword->enabled());
    }

    public function test_accepts_a_password_type_enum()
    {
        config()->set('sensible.'.SetDefaultPassword::class, PasswordType::Pin);

        (new SetDefaultPassword)->configure();

        $this->assertTrue(Validator::make(['password' => '1234'], ['password' => Password::default()])->passes());
        $this->assertTrue(Validator::make(['password' => '12345'], ['password' => Password::default()])->fails());
    }

    public function test_throws_on_invalid_type()
    {
        config()->set('sensible.'.SetDefaultPassword::class, 'simpel');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('must be a boolean or one of [simple, numeric, pin, passphrase, alphanumeric, complex]');

        (new SetDefaultPassword)->enabled();
    }

    public function test_complex_skips_breach_check_during_tests()
    {
        $this->markTestSkippedUnless(
            method_exists(Password::class, 'appliedRules'),
            'The appliedRules method is not available in this version of Laravel.'
        );

        app()->detectEnvironment(fn (): string => 'testing');

        (new SetDefaultPassword)->configure();

        $this->assertFalse(Password::default()->appliedRules()['uncompromised']);
    }
}
