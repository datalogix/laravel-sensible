# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

- `composer test` — run the PHPUnit suite (`vendor/bin/phpunit`; add `--no-coverage` to skip the clover/text coverage report).
- `vendor/bin/phpunit --filter test_name` or `vendor/bin/phpunit tests/Configurables/ForceSchemeTest.php` — run a single test/file.
- `composer lint` — format with Laravel Pint.

CI uses the shared workflow `datalogix/workflows/.github/workflows/laravel-tests.yml@v1`.

## Architecture

Laravel package (`Datalogix\Sensible`, Laravel 12/13) that applies opt-in framework defaults ("configurables").

- `Contracts\Configurable` — `enabled(): bool` + `configure(): void`. Each feature in `src/Configurables/` implements it.
- `SensibleManager` — singleton (aliased `sensible`, exposed via the `Sensible` facade) holding the list of built-in configurables. `run()` resolves each class through the container and calls `configure()` when `enabled()`. Registration dedupes by class name, and configurables registered after `run()` are applied immediately.
- `SensibleServiceProvider` — merges `config/sensible.php` and calls `run()` in `$app->booted(...)`, so configurables registered in any app provider's `register()` or `boot()` are picked up.
- Config keys are the configurable's FQCN (`config('sensible.'.Foo::class)`), read via the `Concerns\ReadsConfiguration` trait. `configEnabled()` accepts boolean-like strings (`1/0`, `on/off`, `yes/no`, `true/false`) and **throws** on anything else. `SetDefaultPassword` additionally accepts a `PasswordType` enum value.
- Defaults in `config/sensible.php` are environment-dependent (`app()->isProduction()`), so they are evaluated when the config is merged. `FakeSleep` and `PreventStrayRequests` additionally only enable when `app()->runningUnitTests()`.

Adding a built-in configurable means: class in `src/Configurables/`, entry in `SensibleManager::$configurables`, entry with `SENSIBLE_*` env var in `config/sensible.php`, row in the README table, and a test in `tests/Configurables/`.

## Tests

- Built on `orchestra/testbench`. `tests/TestCase.php` forces the `production` environment in `resolveApplicationConfiguration()` so config defaults resolve as production before providers register; tests that need other environments call `app()->detectEnvironment(...)` and/or set `config()->set('sensible.'.Foo::class, ...)` explicitly.
- Configurables mutate global/static framework state (URL scheme, Vite, DB, Model guards, etc.); tests reset that state in `setUp()`.
