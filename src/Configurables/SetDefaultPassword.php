<?php

namespace Datalogix\Sensible\Configurables;

use Datalogix\Sensible\Contracts\Configurable;
use Datalogix\Sensible\Enums\PasswordType;
use Illuminate\Validation\Rules\Password;

class SetDefaultPassword implements Configurable
{
    /**
     * Whether the configurable is enabled or not.
     */
    public function enabled(): bool
    {
        return (bool) $this->value();
    }

    /**
     * Run the configurable.
     */
    public function configure(): void
    {
        Password::defaults(fn () => match ($this->type()) {
            PasswordType::Simple => Password::min(6),
            PasswordType::Numeric => Password::min(4)->max(6)->numbers(),
            PasswordType::Pin => Password::min(4)->max(4)->numbers(),
            PasswordType::Passphrase => Password::min(16),
            PasswordType::Alphanumeric => Password::min(8)->max(20)->letters()->numbers(),
            PasswordType::Complex => Password::min(8)->max(20)
                ->mixedCase()->letters()->numbers()->symbols()
                ->uncompromised(),
        });
    }

    /**
     * The raw configuration value, either a boolean (enable/disable) or a password type string.
     */
    protected function value(): bool|string
    {
        return config(sprintf('sensible.%s', self::class), app()->isProduction());
    }

    /**
     * The password type to apply, defaulting to "complex" when only a boolean is configured.
     */
    protected function type(): PasswordType
    {
        $value = $this->value();

        return is_string($value)
            ? PasswordType::tryFrom($value) ?? PasswordType::Complex
            : PasswordType::Complex;
    }
}
