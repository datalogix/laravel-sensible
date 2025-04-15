<?php

namespace Datalogix\Sensible\Configurables;

use Datalogix\Sensible\Contracts\Configurable;
use Illuminate\Validation\Rules\Password;

class SetDefaultPassword implements Configurable
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
        Password::defaults(fn () => Password::min(8)->max(20)
            ->mixedCase()->letters()->numbers()->symbols()
            ->uncompromised()
        );
    }
}
