<?php

namespace Datalogix\Sensible\Concerns;

use InvalidArgumentException;

trait ReadsConfiguration
{
    /**
     * The configuration key for this configurable.
     */
    protected function configKey(): string
    {
        return sprintf('sensible.%s', static::class);
    }

    /**
     * The raw configuration value for this configurable.
     */
    protected function configValue(): mixed
    {
        return config($this->configKey());
    }

    /**
     * Whether the configuration value is truthy, accepting "1", "0", "on", "off", "yes" and "no".
     *
     * The given default is used when the value is not set (null).
     */
    protected function configEnabled(bool $default = false): bool
    {
        $value = $this->configValue();

        if ($value === null) {
            return $default;
        }

        return $this->toBoolean($value)
            ?? throw $this->createInvalidConfigurationValueException('a boolean', $value);
    }

    /**
     * Create the exception for a configuration value that is not one of the expected values.
     */
    protected function createInvalidConfigurationValueException(string $expected, mixed $value): InvalidArgumentException
    {
        return new InvalidArgumentException(sprintf(
            'Configuration value for key [%s] must be %s, [%s] given.',
            $this->configKey(),
            $expected,
            var_export($value, true),
        ));
    }

    /**
     * Convert the given value to a boolean, or null when it is not a boolean-like value.
     */
    protected function toBoolean(mixed $value): ?bool
    {
        return filter_var($value, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);
    }
}
