# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added

- `sensible-config` publish tag: `php artisan vendor:publish --tag="sensible-config"`.
- `ReadsConfiguration::invalidConfigurationValue()` protected helper to build the exception for unsupported configuration values.
- `nesbot/carbon` is now an explicit dependency (`^3.8.4`, the same range Laravel requires).
- README: requirements, `ReadsConfiguration` usage for custom configurables, `PasswordType` enum values in the config, and the license.

### Changed

- `SensibleManager` throws an `InvalidArgumentException` when the container resolves a registered class to something that is not a `Configurable`, instead of a fatal error.
- Environment-dependent defaults (`ForceScheme`, `ProhibitDestructiveCommands`, `SetDefaultPassword`, `ShouldBeStrict`) are `null` in the config and resolved with `app()->isProduction()` when the configurables run.
- `SensibleManager::register()` with an instance replaces an already registered configurable of the same class (including built-ins) instead of silently ignoring it.
- `ReadsConfiguration::configEnabled()` accepts a `$default` used when the configuration value is not set.
- Tests migrated to Pest, Larastan added at level max, and `composer lint` now only checks (`composer format` formats).
- CI migrated to `datalogix/workflows` `package.yml@v2`.
- License holder is now Datalogix.

### Deprecated

- The generic `config` publish tag; use `sensible-config`. It will be removed in the next major version.

### Fixed

- A published `config/sensible.php` no longer crashes the application boot and `php artisan config:cache` with `Target class [env] does not exist`.

### Upgrading

- If you published `config/sensible.php` before this release, it still calls `app()->isProduction()` and keeps crashing. Republish it with `php artisan vendor:publish --tag="sensible-config" --force`, or replace each `app()->isProduction()` default with `null` (e.g. `env('SENSIBLE_FORCE_SCHEME')`).
