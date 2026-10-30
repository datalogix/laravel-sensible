<?php

namespace Datalogix\Sensible;

use Datalogix\Sensible\Contracts\Configurable;
use InvalidArgumentException;

class SensibleManager
{
    /**
     * The list of configurables.
     *
     * @var list<class-string<Configurable>|Configurable>
     */
    protected array $configurables = [
        Configurables\AggressivePrefetching::class,
        Configurables\AutomaticallyEagerLoadRelationships::class,
        Configurables\FakeSleep::class,
        Configurables\ForceScheme::class,
        Configurables\ImmutableDates::class,
        Configurables\PreventStrayRequests::class,
        Configurables\ProhibitDestructiveCommands::class,
        Configurables\SetDefaultPassword::class,
        Configurables\ShouldBeStrict::class,
        Configurables\Unguard::class,
    ];

    /**
     * Whether the configurables have already been run.
     */
    protected bool $ran = false;

    /**
     * Register a new configurable class.
     *
     * An instance replaces an already registered configurable of the same class.
     */
    public function register(string|Configurable $configurable): void
    {
        if (is_string($configurable) && ! is_subclass_of($configurable, Configurable::class)) {
            throw new InvalidArgumentException("{$configurable} must implement ".Configurable::class);
        }

        if (in_array($configurable, $this->configurables, true)) {
            return;
        }

        $class = $this->classOf($configurable);

        if (! in_array($class, array_map($this->classOf(...), $this->configurables), true)) {
            $this->configurables[] = $configurable;
        } elseif (is_string($configurable)) {
            return;
        } else {
            $this->configurables = array_map(
                fn (string|Configurable $registered) => $this->classOf($registered) === $class ? $configurable : $registered,
                $this->configurables,
            );
        }

        if ($this->ran) {
            $this->apply($configurable);
        }
    }

    /**
     * Run configurables.
     */
    public function run(): void
    {
        $this->ran = true;

        foreach ($this->configurables as $configurable) {
            $this->apply($configurable);
        }
    }

    /**
     * Get the class name of the given configurable.
     *
     * @param  class-string<Configurable>|Configurable  $configurable
     * @return class-string
     */
    protected function classOf(string|Configurable $configurable): string
    {
        return is_string($configurable) ? ltrim($configurable, '\\') : $configurable::class;
    }

    /**
     * Configure the given configurable when it is enabled.
     *
     * @param  class-string<Configurable>|Configurable  $configurable
     */
    protected function apply(string|Configurable $configurable): void
    {
        $instance = is_string($configurable) ? app($configurable) : $configurable;

        if (! $instance instanceof Configurable) {
            throw new InvalidArgumentException($this->classOf($configurable).' must resolve to an instance of '.Configurable::class);
        }

        if ($instance->enabled()) {
            $instance->configure();
        }
    }
}
