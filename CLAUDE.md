# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

- `composer test` — run the Pest suite (`vendor/bin/pest`; add `--no-coverage` to skip the clover/text coverage report).
- `vendor/bin/pest --filter "test name"` or `vendor/bin/pest tests/Configurables/ForceSchemeTest.php` — run a single test/file.
- `composer lint` — check only: Pint (`--test`) and Larastan (`phpstan.neon.dist`, level max).
- `composer format` — format with Laravel Pint.

CI (`.github/workflows/datalogix.yml`) uses the shared workflow `datalogix/workflows/.github/workflows/package.yml@v2`.

## Architecture

Laravel package (`Datalogix\Sensible`, PHP ^8.2, Laravel 12/13) that applies opt-in framework defaults ("configurables").

- `Contracts\Configurable` — `enabled(): bool` + `configure(): void`. Each feature in `src/Configurables/` implements it.
- `SensibleManager` — singleton (aliased `sensible`, exposed via the `Sensible` facade) holding the list of built-in configurables. `run()` resolves each class through the container and calls `configure()` when `enabled()`. `register()` accepts a class name or an instance; registration ignores a class name already registered and the very same instance, an instance replaces the entry of the exact same class (a subclass is added alongside), and configurables registered after `run()` are applied immediately (a replacement is applied on top, without undoing the previous one). Classes that don't implement `Configurable`, or container bindings that don't resolve to one, throw `InvalidArgumentException`.
- `SensibleServiceProvider` — merges `config/sensible.php`, publishes it with the `sensible-config` (and legacy `config`) tag, and calls `run()` in `$app->booted(...)`, so configurables registered in any app provider's `register()` or `boot()` are picked up.
- Config keys are the configurable's FQCN (`config('sensible.'.Foo::class)`), read via the `Concerns\ReadsConfiguration` trait. `configEnabled()` accepts boolean-like strings (`1/0`, `on/off`, `yes/no`, `true/false`) and **throws** on anything else (via `invalidConfigurationValue()`). `SetDefaultPassword` also accepts a `PasswordType` value (string, case-insensitive) or enum instance.
- Environment-dependent configurables (`ForceScheme`, `ProhibitDestructiveCommands`, `SetDefaultPassword`, `ShouldBeStrict`) default to `null` in `config/sensible.php` and resolve the default at runtime from `app()->isProduction()`: `configEnabled(default: app()->isProduction())` in `enabled()` (`ShouldBeStrict` negates it), and `SetDefaultPassword::type()` via `configValue() ?? app()->isProduction()`. An empty env value (`''`) is not `null`, so it disables them. The config file must not call `app()`, because a published config is loaded before the environment is detected (e.g. `config:cache`). `FakeSleep` and `PreventStrayRequests` additionally only enable when `app()->runningUnitTests()`.

Adding a built-in configurable means: class in `src/Configurables/`, entry in `SensibleManager::$configurables`, entry with `SENSIBLE_*` env var in `config/sensible.php`, a feature bullet and a row in the README table, and a Pest test in `tests/Configurables/` (plus entries in both datasets of `tests/ConfigTest.php`, the `null` default and the `SENSIBLE_*` variable, when the default depends on the environment).

## Tests

- Pest on top of `orchestra/testbench`. `tests/Pest.php` binds `tests/TestCase.php` to `Concerns/`, `Configurables/` and `SensibleManagerTest.php`; `SensibleServiceProviderTest.php` uses `Fixtures/AppServiceProviderTestCase`; `ConfigTest.php` is unbound and requires `config/sensible.php` directly, toggling `SENSIBLE_*` variables with `putenv()` and restoring them in `finally`. Helper classes live in `tests/Fixtures/`.
- `TestCase` forces the `production` environment, so environment-dependent defaults resolve as production. Tests that need other values call `app()->detectEnvironment(...)` and/or `config()->set('sensible.'.Foo::class, ...)` explicitly.
- Configurables mutate global/static framework state (URL scheme, Vite, DB, Model guards, etc.); tests reset that state in `beforeEach()`.

## Compatibility

- Public API changes need a `@deprecated` alias until the next major.
- The `config` publish tag is kept alongside `sensible-config` until v3.
- Renaming `Configurable::enabled()` to `isEnabled()` is planned for v3 only.
- Issues and pull requests: English
