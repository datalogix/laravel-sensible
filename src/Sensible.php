<?php

namespace Datalogix\Sensible;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void register(string|Configurable $configurable)
 * @method static void run()
 *
 * @see \Datalogix\Sensible\SensibleManager
 */
class Sensible extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'sensible';
    }
}
