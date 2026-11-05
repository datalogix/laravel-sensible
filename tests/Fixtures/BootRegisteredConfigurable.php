<?php

namespace Datalogix\Sensible\Tests\Fixtures;

use Datalogix\Sensible\Contracts\Configurable;

class BootRegisteredConfigurable implements Configurable
{
    public static bool $configured = false;

    public function enabled(): bool
    {
        return true;
    }

    public function configure(): void
    {
        static::$configured = true;
    }
}
