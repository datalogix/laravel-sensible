<?php

namespace Datalogix\Sensible\Configurables;

use Datalogix\Sensible\Concerns\ReadsConfiguration;
use Datalogix\Sensible\Contracts\Configurable;
use Datalogix\Sensible\Enums\PasswordType;
use Illuminate\Validation\Rules\Password;
use InvalidArgumentException;

class SetDefaultPassword implements Configurable
{
    use ReadsConfiguration;

    /**
     * Whether the configurable is enabled or not.
     */
    public function enabled(): bool
    {
        return $this->type() !== null;
    }

    /**
     * Run the configurable.
     */
    public function configure(): void
    {
        $type = $this->type() ?? PasswordType::Complex;

        Password::defaults(fn () => match ($type) {
            PasswordType::Simple => Password::min(6),
            PasswordType::Numeric => Password::min(4)->rules('digits_between:4,6'),
            PasswordType::Pin => Password::min(4)->rules('digits:4'),
            PasswordType::Passphrase => Password::min(16),
            PasswordType::Alphanumeric => Password::min(8)->letters()->numbers(),
            PasswordType::Complex => Password::min(8)
                ->mixedCase()->numbers()->symbols()
                // Skipped in tests: the breach check calls an external API.
                ->unless(app()->runningUnitTests(), fn (Password $password) => $password->uncompromised()),
        });
    }

    /**
     * The password type to apply, or null when disabled.
     *
     * Boolean-like values enable the "complex" type or disable the configurable.
     *
     * @throws InvalidArgumentException
     */
    protected function type(): ?PasswordType
    {
        $value = $this->configValue();

        if ($value instanceof PasswordType) {
            return $value;
        }

        if (is_string($value) && $type = PasswordType::tryFrom(strtolower($value))) {
            return $type;
        }

        return match ($this->toBoolean($value)) {
            true => PasswordType::Complex,
            false => null,
            null => throw new InvalidArgumentException(sprintf(
                'Configuration value for key [%s] must be a boolean or one of [%s], [%s] given.',
                $this->configKey(),
                implode(', ', array_column(PasswordType::cases(), 'value')),
                var_export($value, true),
            )),
        };
    }
}
