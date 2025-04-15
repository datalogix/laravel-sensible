<?php

namespace Datalogix\Sensible\Configurables;

use Datalogix\Sensible\Contracts\Configurable;
use Illuminate\Database\Eloquent\Model;

class Unguard implements Configurable
{
    /**
     * Whether the configurable is enabled or not.
     */
    public function enabled(): bool
    {
        return config()->boolean(sprintf('sensible.%s', self::class), false);
    }

    /**
     * Run the configurable.
     */
    public function configure(): void
    {
        Model::unguard();
    }
}
