<?php

use Datalogix\Sensible\Configurables\SetDefaultPassword;
use Datalogix\Sensible\Enums\PasswordType;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

beforeEach(function () {
    Password::$defaultCallback = null;
});

test('configure', function () {
    $this->markTestSkippedUnless(
        method_exists(Password::class, 'appliedRules'),
        'The appliedRules method is not available in this version of Laravel.'
    );

    $setDefaultPassword = new SetDefaultPassword;
    $setDefaultPassword->configure();

    $passwordRules = Password::default()->appliedRules();

    expect(8)->toEqual($passwordRules['min']);
    expect($passwordRules['max'])->toBeNull();
    expect($passwordRules['mixedCase'])->toBeTrue();
    expect($passwordRules['numbers'])->toBeTrue();
    expect($passwordRules['symbols'])->toBeTrue();
    expect($passwordRules['uncompromised'])->toBeTrue();
});

test('configure simple', function () {
    $this->markTestSkippedUnless(
        method_exists(Password::class, 'appliedRules'),
        'The appliedRules method is not available in this version of Laravel.'
    );

    config()->set('sensible.'.SetDefaultPassword::class, 'simple');

    $setDefaultPassword = new SetDefaultPassword;
    $setDefaultPassword->configure();

    $passwordRules = Password::default()->appliedRules();

    expect(6)->toEqual($passwordRules['min']);
    expect($passwordRules['mixedCase'])->toBeFalse();
    expect($passwordRules['letters'])->toBeFalse();
    expect($passwordRules['numbers'])->toBeFalse();
    expect($passwordRules['symbols'])->toBeFalse();
    expect($passwordRules['uncompromised'])->toBeFalse();
});

test('configure numeric', function () {
    $this->markTestSkippedUnless(
        method_exists(Password::class, 'appliedRules'),
        'The appliedRules method is not available in this version of Laravel.'
    );

    config()->set('sensible.'.SetDefaultPassword::class, 'numeric');

    $setDefaultPassword = new SetDefaultPassword;
    $setDefaultPassword->configure();

    $passwordRules = Password::default()->appliedRules();

    expect(4)->toEqual($passwordRules['min']);
    expect($passwordRules['customRules'])->toBe(['digits_between:4,6']);
});

test('configure pin', function () {
    $this->markTestSkippedUnless(
        method_exists(Password::class, 'appliedRules'),
        'The appliedRules method is not available in this version of Laravel.'
    );

    config()->set('sensible.'.SetDefaultPassword::class, 'pin');

    $setDefaultPassword = new SetDefaultPassword;
    $setDefaultPassword->configure();

    $passwordRules = Password::default()->appliedRules();

    expect(4)->toEqual($passwordRules['min']);
    expect($passwordRules['customRules'])->toBe(['digits:4']);
});

test('configure passphrase', function () {
    $this->markTestSkippedUnless(
        method_exists(Password::class, 'appliedRules'),
        'The appliedRules method is not available in this version of Laravel.'
    );

    config()->set('sensible.'.SetDefaultPassword::class, 'passphrase');

    $setDefaultPassword = new SetDefaultPassword;
    $setDefaultPassword->configure();

    $passwordRules = Password::default()->appliedRules();

    expect(16)->toEqual($passwordRules['min']);
    expect($passwordRules['mixedCase'])->toBeFalse();
    expect($passwordRules['letters'])->toBeFalse();
    expect($passwordRules['numbers'])->toBeFalse();
    expect($passwordRules['symbols'])->toBeFalse();
    expect($passwordRules['uncompromised'])->toBeFalse();
});

test('configure alphanumeric', function () {
    $this->markTestSkippedUnless(
        method_exists(Password::class, 'appliedRules'),
        'The appliedRules method is not available in this version of Laravel.'
    );

    config()->set('sensible.'.SetDefaultPassword::class, 'alphanumeric');

    $setDefaultPassword = new SetDefaultPassword;
    $setDefaultPassword->configure();

    $passwordRules = Password::default()->appliedRules();

    expect(8)->toEqual($passwordRules['min']);
    expect($passwordRules['max'])->toBeNull();
    expect($passwordRules['letters'])->toBeTrue();
    expect($passwordRules['numbers'])->toBeTrue();
    expect($passwordRules['mixedCase'])->toBeFalse();
    expect($passwordRules['symbols'])->toBeFalse();
    expect($passwordRules['uncompromised'])->toBeFalse();
});

test('is enabled by default', function () {
    $setDefaultPassword = new SetDefaultPassword;

    expect($setDefaultPassword->enabled())->toBeTrue();
});

test('is enabled when a type is configured', function () {
    config()->set('sensible.'.SetDefaultPassword::class, 'simple');

    $setDefaultPassword = new SetDefaultPassword;

    expect($setDefaultPassword->enabled())->toBeTrue();
});

test('can be disabled', function () {
    config()->set('sensible.'.SetDefaultPassword::class, false);

    $setDefaultPassword = new SetDefaultPassword;

    expect($setDefaultPassword->enabled())->toBeFalse();
});

test('pin rejects non digits', function () {
    config()->set('sensible.'.SetDefaultPassword::class, 'pin');

    (new SetDefaultPassword)->configure();

    expect(Validator::make(['password' => '1234'], ['password' => Password::default()])->passes())->toBeTrue();
    expect(Validator::make(['password' => 'abc1'], ['password' => Password::default()])->fails())->toBeTrue();
});

test('accepts boolean like strings', function () {
    $setDefaultPassword = new SetDefaultPassword;

    config()->set('sensible.'.SetDefaultPassword::class, '1');
    expect($setDefaultPassword->enabled())->toBeTrue();

    config()->set('sensible.'.SetDefaultPassword::class, 'off');
    expect($setDefaultPassword->enabled())->toBeFalse();
});

test('accepts a password type enum', function () {
    config()->set('sensible.'.SetDefaultPassword::class, PasswordType::Pin);

    (new SetDefaultPassword)->configure();

    expect(Validator::make(['password' => '1234'], ['password' => Password::default()])->passes())->toBeTrue();
    expect(Validator::make(['password' => '12345'], ['password' => Password::default()])->fails())->toBeTrue();
});

test('throws on invalid type', function () {
    config()->set('sensible.'.SetDefaultPassword::class, 'simpel');

    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage('must be a boolean or one of [simple, numeric, pin, passphrase, alphanumeric, complex]');

    (new SetDefaultPassword)->enabled();
});

test('complex skips breach check during tests', function () {
    $this->markTestSkippedUnless(
        method_exists(Password::class, 'appliedRules'),
        'The appliedRules method is not available in this version of Laravel.'
    );

    app()->detectEnvironment(fn (): string => 'testing');

    (new SetDefaultPassword)->configure();

    expect(Password::default()->appliedRules()['uncompromised'])->toBeFalse();
});
