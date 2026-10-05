<?php

namespace Datalogix\Sensible\Configurables;

use Datalogix\Sensible\Concerns\ReadsConfiguration;
use Datalogix\Sensible\Contracts\Configurable;
use Illuminate\Support\Sleep;

class FakeSleep implements Configurable
{
    use ReadsConfiguration;

    /**
     * Whether the configurable is enabled or not.
     */
    public function enabled(): bool
    {
        return $this->configEnabled()
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
