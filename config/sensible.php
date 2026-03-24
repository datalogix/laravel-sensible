<?php

use Datalogix\Sensible\Configurables\AggressivePrefetching;
use Datalogix\Sensible\Configurables\AutomaticallyEagerLoadRelationships;
use Datalogix\Sensible\Configurables\FakeSleep;
use Datalogix\Sensible\Configurables\ForceScheme;
use Datalogix\Sensible\Configurables\ImmutableDates;
use Datalogix\Sensible\Configurables\PreventStrayRequests;
use Datalogix\Sensible\Configurables\ProhibitDestructiveCommands;
use Datalogix\Sensible\Configurables\SetDefaultPassword;
use Datalogix\Sensible\Configurables\ShouldBeStrict;
use Datalogix\Sensible\Configurables\Unguard;

return [
    /**
     * 🚀 Asset Prefetching.
     *
     * Configures Laravel Vite to preload assets more aggressively.
     * Improves front-end load times and user experience.
     */
    AggressivePrefetching::class => env('SENSIBLE_AGGRESSIVE_PREFETCHING', true),

    /**
     * ⚡️ Auto Eager Loading.
     *
     * Automatically eager loads relationships defined in the model’s `$with` property.
     * Reduces N+1 query issues without needing to call `with()` manually.
     */
    AutomaticallyEagerLoadRelationships::class => env('SENSIBLE_AUTOMATICALLY_EAGER_LOAD_RELATIONSHIPS', true),

    /**
     * 😴 Fake Sleep.
     *
     * Configures Laravel's Sleep Facade to be faked during tests.
     * Prevents actual delays, ensuring faster test execution.
     */
    FakeSleep::class => env('SENSIBLE_FAKE_SLEEP', true),

    /**
     * 🔒 Force HTTPS.
     *
     * Forces all generated URLs to use `https://`.
     * Recommended in production to ensure secure connections.
     */
    ForceScheme::class => env('SENSIBLE_FORCE_SCHEME', app()->isProduction()),

    /**
     * 🕒 Immutable Dates.
     *
     * Uses `CarbonImmutable` instead of mutable date objects.
     * Prevents unexpected mutations and ensures consistency.
     */
    ImmutableDates::class => env('SENSIBLE_IMMUTABLE_DATES', true),

    /**
     * 🔄 Prevent Stray Requests.
     *
     * Ensures all HTTP calls during testing are explicitly mocked.
     * Prevents accidental external requests in tests.
     */
    PreventStrayRequests::class => env('SENSIBLE_PREVENT_STRAY_REQUESTS', true),

    /**
     * 🛑 Safe Console.
     *
     * Blocks potentially dangerous Artisan commands (e.g., `migrate:fresh`) in production.
     * Adds a layer of safety in critical environments.
     */
    ProhibitDestructiveCommands::class => env('SENSIBLE_PROHIBIT_DESTRUCTIVE_COMMANDS', app()->isProduction()),

    /**
     * 🔑 Set Default Password Strategy.
     *
     * Configures a default password policy to enforce strong passwords when creating or seeding users.
     * Ensures passwords meet the following criteria:
     * - Minimum length of 8 characters.
     * - Maximum length of 20 characters.
     * - Must include mixed case letters, numbers, and symbols.
     * - Ensures password is not compromised.
     */
    SetDefaultPassword::class => env('SENSIBLE_SET_DEFAULT_PASSWORD', app()->isProduction()),

    /**
     * ✅ Strict Models.
     *
     * Enforces strict handling of attributes in Eloquent:
     * - Accessing missing attributes throws exceptions.
     * - Lazy loading is disabled by default.
     * - Assigning undefined attributes is not allowed.
     */
    ShouldBeStrict::class => env('SENSIBLE_SHOULD_BE_STRICT', ! app()->isProduction()),

    /**
     * 🔓 Optional Unguarded Models.
     *
     * Disables mass-assignment protection globally (use with caution).
     * Useful in trusted or local development environments.
     */
    Unguard::class => env('SENSIBLE_UNGUARD', false),
];
