<?php

namespace Datalogix\Sensible\Configurables;

use Carbon\CarbonImmutable;
use Datalogix\Sensible\Contracts\Configurable;
use Illuminate\Support\Facades\Date;

class ImmutableDates implements Configurable
{
    /**
     * Whether the configurable is enabled or not.
     */
    public function enabled(): bool
    {
        return config()->boolean(sprintf('sensible.%s', self::class), true);
    }

    /**
     * Run the configurable.
     */
    public function configure(): void
    {
        Date::use(CarbonImmutable::class);
    }
}
