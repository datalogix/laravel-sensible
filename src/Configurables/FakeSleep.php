<?php

namespace Datalogix\Sensible\Configurables;

use Datalogix\Sensible\Contracts\Configurable;
use Illuminate\Support\Sleep;

class FakeSleep implements Configurable
{
    /**
     * Whether the configurable is enabled or not.
     */
    public function enabled(): bool
    {
        return config()->boolean(sprintf('sensible.%s', self::class), true)
        && app()->runningUnitTests();
    }

    /**
     * Run the configurable.
     */
    public function configure(): void
    {
        Sleep::fake();
    }
}
