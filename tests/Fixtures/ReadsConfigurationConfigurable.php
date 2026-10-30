<?php

namespace Datalogix\Sensible\Tests\Fixtures;

use Datalogix\Sensible\Concerns\ReadsConfiguration;

class ReadsConfigurationConfigurable
{
    use ReadsConfiguration;

    public function key(): string
    {
        return $this->configKey();
    }

    public function isEnabled(): bool
    {
        return $this->configEnabled();
    }
}
