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
     * When a relationship is lazy loaded on a model retrieved as part of a collection,
     * it is loaded for every model in that collection at once (Laravel 12.8+).
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
     * Configures a default password policy to enforce when creating or seeding users.
     * Accepts `true`/`false` to enable/disable (defaults to the "complex" policy), or one of
     * the `PasswordType` values below:
     * - `simple`       – Minimum length of 6 characters only, no other requirements.
     * - `numeric`      – Digits only, 4 to 6 digits.
     * - `pin`          – Digits only, exactly 4 digits.
     * - `passphrase`   – Minimum length of 16 characters only, no other requirements.
     * - `alphanumeric` – Minimum length of 8, requires letters and numbers.
     * - `complex`      – Minimum length of 8, mixed case letters, numbers, symbols,
     *   and checked against known data breaches (default when only `true` is set).
     *   The breach check is skipped in tests, since it calls an external API.
     *
     * Any other value throws an exception, so typos don't silently change the policy.
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
