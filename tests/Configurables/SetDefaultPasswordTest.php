<?php

use Datalogix\Sensible\Configurables\SetDefaultPassword;
use Datalogix\Sensible\Enums\PasswordType;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

beforeEach(function () {
    Password::$defaultCallback = null;
});

describe('applied rules', function () {
    beforeEach(function () {
        $this->markTestSkippedUnless(
            method_exists(Password::class, 'appliedRules'),
            'The appliedRules method is not available in this version of Laravel.'
        );
    });

    test('configure', function () {
        $setDefaultPassword = new SetDefaultPassword;
        $setDefaultPassword->configure();

        $passwordRules = Password::default()->appliedRules();

        expect($passwordRules['min'])->toBe(8);
        expect($passwordRules['max'])->toBeNull();
        expect($passwordRules['mixedCase'])->toBeTrue();
        expect($passwordRules['numbers'])->toBeTrue();
        expect($passwordRules['symbols'])->toBeTrue();
        expect($passwordRules['uncompromised'])->toBeTrue();
    });

    test('configure simple', function () {
        config()->set('sensible.'.SetDefaultPassword::class, 'simple');

        $setDefaultPassword = new SetDefaultPassword;
        $setDefaultPassword->configure();

        $passwordRules = Password::default()->appliedRules();

        expect($passwordRules['min'])->toBe(6);
        expect($passwordRules['mixedCase'])->toBeFalse();
        expect($passwordRules['letters'])->toBeFalse();
        expect($passwordRules['numbers'])->toBeFalse();
        expect($passwordRules['symbols'])->toBeFalse();
        expect($passwordRules['uncompromised'])->toBeFalse();
    });

    test('configure numeric', function () {
        config()->set('sensible.'.SetDefaultPassword::class, 'numeric');

        $setDefaultPassword = new SetDefaultPassword;
        $setDefaultPassword->configure();

        $passwordRules = Password::default()->appliedRules();

        expect($passwordRules['min'])->toBe(4);
        expect($passwordRules['customRules'])->toBe(['digits_between:4,6']);
    });

    test('configure pin', function () {
        config()->set('sensible.'.SetDefaultPassword::class, 'pin');

        $setDefaultPassword = new SetDefaultPassword;
        $setDefaultPassword->configure();

        $passwordRules = Password::default()->appliedRules();

        expect($passwordRules['min'])->toBe(4);
        expect($passwordRules['customRules'])->toBe(['digits:4']);
    });

    test('configure passphrase', function () {
        config()->set('sensible.'.SetDefaultPassword::class, 'passphrase');

        $setDefaultPassword = new SetDefaultPassword;
        $setDefaultPassword->configure();

        $passwordRules = Password::default()->appliedRules();

        expect($passwordRules['min'])->toBe(16);
        expect($passwordRules['mixedCase'])->toBeFalse();
        expect($passwordRules['letters'])->toBeFalse();
        expect($passwordRules['numbers'])->toBeFalse();
        expect($passwordRules['symbols'])->toBeFalse();
        expect($passwordRules['uncompromised'])->toBeFalse();
    });

    test('configure alphanumeric', function () {
        config()->set('sensible.'.SetDefaultPassword::class, 'alphanumeric');

        $setDefaultPassword = new SetDefaultPassword;
        $setDefaultPassword->configure();

        $passwordRules = Password::default()->appliedRules();

        expect($passwordRules['min'])->toBe(8);
        expect($passwordRules['max'])->toBeNull();
        expect($passwordRules['letters'])->toBeTrue();
        expect($passwordRules['numbers'])->toBeTrue();
        expect($passwordRules['mixedCase'])->toBeFalse();
        expect($passwordRules['symbols'])->toBeFalse();
        expect($passwordRules['uncompromised'])->toBeFalse();
    });

    test('configure is case insensitive', function () {
        config()->set('sensible.'.SetDefaultPassword::class, 'PIN');

        (new SetDefaultPassword)->configure();

        expect(Password::default()->appliedRules()['customRules'])->toBe(['digits:4']);
    });

    test('complex skips breach check during tests', function () {
        app()->detectEnvironment(fn (): string => 'testing');

        (new SetDefaultPassword)->configure();

        expect(Password::default()->appliedRules()['uncompromised'])->toBeFalse();
    });
});

test('is enabled by default in production', function () {
    $setDefaultPassword = new SetDefaultPassword;

    expect($setDefaultPassword->enabled())->toBeTrue();
});

test('is disabled by default outside production', function () {
    app()->detectEnvironment(fn (): string => 'local');

    $setDefaultPassword = new SetDefaultPassword;

    expect($setDefaultPassword->enabled())->toBeFalse();
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

test('can be enabled outside production', function (bool|string $value) {
    config()->set('sensible.'.SetDefaultPassword::class, $value);
    app()->detectEnvironment(fn (): string => 'local');

    $setDefaultPassword = new SetDefaultPassword;

    expect($setDefaultPassword->enabled())->toBeTrue();
})->with([true, 'simple']);

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

test('an empty value disables it', function () {
    config()->set('sensible.'.SetDefaultPassword::class, '');

    expect((new SetDefaultPassword)->enabled())->toBeFalse();
});

test('numeric accepts 4 to 6 digits only', function (string $password, bool $passes) {
    config()->set('sensible.'.SetDefaultPassword::class, 'numeric');

    (new SetDefaultPassword)->configure();

    expect(Validator::make(['password' => $password], ['password' => Password::default()])->passes())->toBe($passes);
})->with([
    ['123', false],
    ['1234', true],
    ['123456', true],
    ['1234567', false],
    ['12a4', false],
]);

test('accepts a password type enum', function () {
    config()->set('sensible.'.SetDefaultPassword::class, PasswordType::Pin);

    (new SetDefaultPassword)->configure();

    expect(Validator::make(['password' => '1234'], ['password' => Password::default()])->passes())->toBeTrue();
    expect(Validator::make(['password' => '12345'], ['password' => Password::default()])->fails())->toBeTrue();
});

test('throws on invalid type', function () {
    config()->set('sensible.'.SetDefaultPassword::class, 'simpel');

    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage(sprintf(
        "Configuration value for key [sensible.%s] must be a boolean or one of [simple, numeric, pin, passphrase, alphanumeric, complex], ['simpel'] given.",
        SetDefaultPassword::class,
    ));

    (new SetDefaultPassword)->enabled();
});
