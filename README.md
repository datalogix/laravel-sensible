# Laravel Sensible

[![Latest Stable Version](https://poser.pugx.org/datalogix/laravel-sensible/version)](https://packagist.org/packages/datalogix/laravel-sensible)
[![Total Downloads](https://poser.pugx.org/datalogix/laravel-sensible/downloads)](https://packagist.org/packages/datalogix/laravel-sensible)
[![datalogix](https://github.com/datalogix/laravel-sensible/actions/workflows/datalogix.yml/badge.svg)](https://github.com/datalogix/laravel-sensible/actions/workflows/datalogix.yml)
[![codecov](https://codecov.io/gh/datalogix/laravel-sensible/branch/main/graph/badge.svg)](https://codecov.io/gh/datalogix/laravel-sensible)
[![License](https://poser.pugx.org/datalogix/laravel-sensible/license)](https://packagist.org/packages/datalogix/laravel-sensible)

> Laravel Sensible is a lightweight utility package for applying smart defaults and common best practices in everyday Laravel development.

## Requirements

- PHP 8.2+
- Laravel 12 or 13

## Installation

You can install the package via composer:

```bash
composer require datalogix/laravel-sensible
```

The package will automatically register itself.

## Features

All features are optional and fully configurable via `config/sensible.php`:

- 🚀 **Asset Prefetching** – Prefetch assets for faster load times.
- ⚡️ **Auto Eager Loading** – Avoid N+1 queries automatically (Laravel 12.8+; skipped on older versions).
- 😴 **Fake Sleep** – Fakes Laravel's `Sleep` class in tests, preventing real delays.
- 🔒 **Force HTTPS** – Generate all URLs with `https://` (plain HTTP requests are not redirected).
- 🕒 **Immutable Dates** – Prevent unexpected date mutations.
- 🔄 **Prevent Stray Requests** – Block unmocked HTTP requests in tests.
- 🛑 **Safe Console** – Block destructive database commands (e.g. `migrate:fresh`).
- 🔑 **Set Default Password** – Set the rules returned by `Password::defaults()` in validation.
- ✅ **Strict Models** – Enforce strict model behavior.
- 🔓 **Unguarded Models** – Disable mass-assignment protection (off by default).

## Configuration

You can publish the config file using the command:

```bash
php artisan vendor:publish --tag="sensible-config"
```

This will create a `config/sensible.php` file where you can enable or disable individual features:

```php
// config/sensible.php

return [
    \Datalogix\Sensible\Configurables\Unguard::class => false,
    // other configurables...
];
```

Each feature can also be toggled through an environment variable. Values such as `true`/`false`, `1`/`0`, `on`/`off` and `yes`/`no` are accepted; anything else throws an exception.

| Feature                             | Environment variable                              | Default                   |
| ----------------------------------- | ------------------------------------------------- | ------------------------- |
| Asset Prefetching                   | `SENSIBLE_AGGRESSIVE_PREFETCHING`                 | `true`                    |
| Auto Eager Loading                  | `SENSIBLE_AUTOMATICALLY_EAGER_LOAD_RELATIONSHIPS` | `true`                    |
| Fake Sleep (tests only)             | `SENSIBLE_FAKE_SLEEP`                             | `true`                    |
| Force HTTPS                         | `SENSIBLE_FORCE_SCHEME`                           | `true` in production      |
| Immutable Dates                     | `SENSIBLE_IMMUTABLE_DATES`                        | `true`                    |
| Prevent Stray Requests (tests only) | `SENSIBLE_PREVENT_STRAY_REQUESTS`                 | `true`                    |
| Safe Console                        | `SENSIBLE_PROHIBIT_DESTRUCTIVE_COMMANDS`          | `true` in production      |
| Set Default Password                | `SENSIBLE_SET_DEFAULT_PASSWORD`                   | `true` in production      |
| Strict Models                       | `SENSIBLE_SHOULD_BE_STRICT`                       | `true` outside production |
| Unguarded Models                    | `SENSIBLE_UNGUARD`                                | `false`                   |

"In production" means `app()->isProduction()` when the configurables run (after the application boots), so the `--env=` Artisan option is respected. Setting the `SENSIBLE_*` variable overrides the environment-based default. Leave it unset (or remove it) to keep that default: an empty value such as `SENSIBLE_FORCE_SCHEME=` counts as false and disables the feature. A `null` value (e.g. `SENSIBLE_FORCE_SCHEME=null`) counts as not set: it keeps the environment-based default and disables the other features.

### Password policies

`SENSIBLE_SET_DEFAULT_PASSWORD` accepts a boolean-like value (`true` applies the `complex` policy, `false` disables it) or one of the following policies (case-insensitive):

| Policy         | Rules                                                                                                    |
| -------------- | -------------------------------------------------------------------------------------------------------- |
| `simple`       | At least 6 characters.                                                                                   |
| `numeric`      | Digits only, 4 to 6 characters.                                                                          |
| `pin`          | Digits only, exactly 4 characters.                                                                       |
| `passphrase`   | At least 16 characters.                                                                                  |
| `alphanumeric` | At least 8 characters, with letters and numbers.                                                         |
| `complex`      | At least 8 characters, mixed case, numbers, symbols and not compromised (breach check skipped in tests). |

In `config/sensible.php` you can also use the enum, e.g. `SetDefaultPassword::class => PasswordType::Pin` (`Datalogix\Sensible\Enums\PasswordType`). Any other value throws an exception. The policy only applies where your validation rules use `Password::defaults()`.

## Custom configurables

You can register your own configurables by implementing `Datalogix\Sensible\Contracts\Configurable`:

```php
// app/Configurables/DefaultStringLength.php

namespace App\Configurables;

use Datalogix\Sensible\Contracts\Configurable;
use Illuminate\Support\Facades\Schema;

class DefaultStringLength implements Configurable
{
    public function enabled(): bool
    {
        return true;
    }

    public function configure(): void
    {
        Schema::defaultStringLength(191);
    }
}
```

```php
// app/Providers/AppServiceProvider.php

use App\Configurables\DefaultStringLength;
use Datalogix\Sensible\Sensible;

public function boot(): void
{
    Sensible::register(DefaultStringLength::class);
}
```

`register()` accepts a class name (resolved through the container) or an instance. Classes that don't implement `Configurable`, or container bindings that don't resolve to one, throw an `InvalidArgumentException`.

Configurables run once every service provider has booted, so registering them in `register()` or `boot()` both work. Configurables registered later are applied immediately, and registering the same class name twice has no effect. Registering an instance replaces the configurable of the exact same class, including a built-in one (e.g. `new ForceScheme`); registering the same instance again has no effect. Matching is by exact class: a subclass of a built-in is added alongside it and reads the configuration under its own class name. Give it its own entry in `config/sensible.php`: without one it falls back to the default in code, which is disabled unless the built-in's default depends on the environment. Disable the built-in in `config/sensible.php` if only yours should run. If the configurables have already run, the replacement is applied on top of the previous one; the previous configurable's effects are not undone.

### Reading the configuration

Use the `Datalogix\Sensible\Concerns\ReadsConfiguration` trait to toggle your configurable from `config/sensible.php`, under its class name, with the same boolean-like values as the built-in ones:

```php
// app/Configurables/DefaultStringLength.php

namespace App\Configurables;

use Datalogix\Sensible\Concerns\ReadsConfiguration;
use Datalogix\Sensible\Contracts\Configurable;
use Illuminate\Support\Facades\Schema;

class DefaultStringLength implements Configurable
{
    use ReadsConfiguration;

    public function enabled(): bool
    {
        return $this->configEnabled();
    }

    public function configure(): void
    {
        Schema::defaultStringLength(191);
    }
}
```

```php
// config/sensible.php

use App\Configurables\DefaultStringLength;

return [
    DefaultStringLength::class => env('SENSIBLE_DEFAULT_STRING_LENGTH', true),
    // other configurables...
];
```

The key must be the configurable's fully qualified class name, so import the class in the config file; a missing or mismatched key silently falls back to the `configEnabled()` default (disabled unless you pass one). `configValue()` returns the raw value, and `createInvalidConfigurationValueException()` builds the exception for values you don't accept. `toBoolean()` converts a boolean-like value (or returns `null` when it isn't one; `null` and an empty string convert to `false`, so check for a missing value first), and `configKey()` returns the configuration key, which you can override to read from another key.

`configEnabled()` accepts a default used when the value is not set (`null`), which lets your configurable depend on the environment like the built-in ones:

```php
public function enabled(): bool
{
    return $this->configEnabled(default: app()->isLocal());
}
```

```php
// config/sensible.php

DefaultStringLength::class => env('SENSIBLE_DEFAULT_STRING_LENGTH'),
```

Don't call `app()` in `config/sensible.php`: the published config is loaded before the environment is detected.

## Release notes

See [GitHub Releases](https://github.com/datalogix/laravel-sensible/releases).

## License

MIT. See [LICENSE](LICENSE).
