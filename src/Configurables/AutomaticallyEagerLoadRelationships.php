<?php

namespace Datalogix\Sensible\Configurables;

use Datalogix\Sensible\Concerns\ReadsConfiguration;
use Datalogix\Sensible\Contracts\Configurable;
use Illuminate\Database\Eloquent\Model;

class AutomaticallyEagerLoadRelationships implements Configurable
{
    use ReadsConfiguration;

    /**
     * Whether the configurable is enabled or not.
     */
    public function enabled(): bool
    {
        // Available since Laravel 12.8.
        return $this->configEnabled()
            && method_exists(Model::class, 'automaticallyEagerLoadRelationships');
    }

    /**
     * Run the configurable.
     */
    public function configure(): void
    {
        Model::automaticallyEagerLoadRelationships();
    }
}
