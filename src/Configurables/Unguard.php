<?php

namespace Datalogix\Sensible\Configurables;

use Datalogix\Sensible\Concerns\ReadsConfiguration;
use Datalogix\Sensible\Contracts\Configurable;
use Illuminate\Database\Eloquent\Model;

class Unguard implements Configurable
{
    use ReadsConfiguration;

    /**
     * Whether the configurable is enabled or not.
     */
    public function enabled(): bool
    {
        return $this->configEnabled();
    }

    /**
     * Run the configurable.
     */
    public function configure(): void
    {
        Model::unguard();
    }
}
