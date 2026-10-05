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
     */
    protected function configEnabled(): bool
    {
        return $this->toBoolean($this->configValue())
            ?? throw new InvalidArgumentException(sprintf(
                'Configuration value for key [%s] must be a boolean, [%s] given.',
                $this->configKey(),
                var_export($this->configValue(), true),
            ));
    }

    /**
     * Convert the given value to a boolean, or null when it is not a boolean-like value.
     */
    protected function toBoolean(mixed $value): ?bool
    {
        if ($value === null) {
            return false;
        }

        return filter_var($value, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);
    }
}
