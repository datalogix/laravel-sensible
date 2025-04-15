<?php

namespace Datalogix\Sensible\Configurables;

use Datalogix\Sensible\Contracts\Configurable;
use Illuminate\Database\Eloquent\Model;

class AutomaticallyEagerLoadRelationships implements Configurable
{
    /**
     * Whether the configurable is enabled or not.
     */
    public function enabled(): bool
    {
        return method_exists(Model::class, 'automaticallyEagerLoadRelationships')
            && config()->boolean(sprintf('sensible.%s', self::class), true);
    }

    /**
     * Run the configurable.
     */
    public function configure(): void
    {
        Model::automaticallyEagerLoadRelationships();
    }
}
