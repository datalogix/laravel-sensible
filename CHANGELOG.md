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
- Environment-dependent defaults (`ForceScheme`, `ProhibitDestructiveCommands`, `SetDefaultPassword`, `ShouldBeStrict`) are read from `APP_ENV` (a missing value counts as production); the `--env=` Artisan option no longer affects them.
- Tests migrated to Pest, Larastan added at level max, and `composer lint` now only checks (`composer format` formats).
- CI migrated to `datalogix/workflows` `package.yml@v2`.
- License holder is now Datalogix.

### Deprecated

- The generic `config` publish tag; use `sensible-config`. It will be removed in the next major version.

### Fixed

- A published `config/sensible.php` no longer crashes the application boot and `php artisan config:cache` with `Target class [env] does not exist`.
