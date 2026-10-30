<?php

namespace Datalogix\Sensible\Tests\Fixtures;

use Datalogix\Sensible\Contracts\Configurable;

class TestConfigurable implements Configurable
{
    public bool $configured = false;

    public function enabled(): bool
    {
        return true;
    }

    public function configure(): void
    {
        $this->configured = true;
    }
}
