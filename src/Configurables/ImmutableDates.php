<?php

namespace Datalogix\Sensible\Configurables;

use Carbon\CarbonImmutable;
use Datalogix\Sensible\Concerns\ReadsConfiguration;
use Datalogix\Sensible\Contracts\Configurable;
use Illuminate\Support\Facades\Date;

class ImmutableDates implements Configurable
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
        Date::use(CarbonImmutable::class);
    }
}
