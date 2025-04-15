<?php

namespace Datalogix\Sensible;

use Datalogix\Sensible\Contracts\Configurable;

class SensibleManager
{
    /**
     * The list of configurables.
     *
     * @var list<class-string<Configurable>>
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
     * Register a new configurable class.
     */
    public function register(string|Configurable $configurable): void
    {
        $class = is_string($configurable) ? $configurable : get_class($configurable);

        if (! is_subclass_of($class, Configurable::class)) {
            throw new \InvalidArgumentException("{$class} must implement ".Configurable::class);
        }

        $this->configurables[] = $class;
    }

    /**
     * Run configurables.
     */
    public function run(): void
    {
        collect($this->configurables)
            ->map(fn (string $class) => app($class))
            ->filter(fn (Configurable $configurable) => $configurable->enabled())
            ->each(fn (Configurable $configurable) => $configurable->configure());
    }
}
