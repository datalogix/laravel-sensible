<?php

namespace Datalogix\Sensible\Configurables;

use Datalogix\Sensible\Contracts\Configurable;
use Illuminate\Support\Facades\URL;

class ForceScheme implements Configurable
{
    /**
     * Whether the configurable is enabled or not.
     */
    public function enabled(): bool
    {
        return config()->boolean(sprintf('sensible.%s', self::class), app()->isProduction());
    }

    /**
     * Run the configurable.
     */
    public function configure(): void
    {
        URL::forceScheme('https');
    }
}
