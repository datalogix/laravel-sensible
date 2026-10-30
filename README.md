# Laravel Sensible

[![Latest Stable Version](https://poser.pugx.org/datalogix/laravel-sensible/version)](https://packagist.org/packages/datalogix/laravel-sensible)
[![Total Downloads](https://poser.pugx.org/datalogix/laravel-sensible/downloads)](https://packagist.org/packages/datalogix/laravel-sensible)
[![tests](https://github.com/datalogix/laravel-sensible/actions/workflows/datalogix.yml/badge.svg)](https://github.com/datalogix/laravel-sensible/actions/workflows/datalogix.yml)
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
- 😴 **Fake Sleep** – Mocks the delay function in tests, preventing real delays.
- 🔒 **Force HTTPS** – Enforce secure `https://` URLs.
- 🕒 **Immutable Dates** – Prevent unexpected date mutations.
- 🔄 **Prevent Stray Requests** – Block unmocked HTTP requests in tests.
- 🛑 **Safe Console** – Block destructive database commands (e.g. `migrate:fresh`).
- 🔑 **Set Default Password** – Enforce strong password policies.
- ✅ **Strict Models** – Enforce strict model behavior.
- 🔓 **Optional Unguarded Models** – Disable mass-assignment protection.

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

"In production" means `app()->isProduction()` when the configurables run (after the application boots), so the `--env=` Artisan option is respected. Setting the `SENSIBLE_*` variable overrides the environment-based default.

### Password policies

`SENSIBLE_SET_DEFAULT_PASSWORD` accepts a boolean (which applies the `complex` policy) or one of the following policies:

| Policy         | Rules                                                                                                    |
| -------------- | -------------------------------------------------------------------------------------------------------- |
| `simple`       | At least 6 characters.                                                                                   |
| `numeric`      | Digits only, 4 to 6 characters.                                                                          |
| `pin`          | Digits only, exactly 4 characters.                                                                       |
| `passphrase`   | At least 16 characters.                                                                                  |
| `alphanumeric` | At least 8 characters, with letters and numbers.                                                         |
| `complex`      | At least 8 characters, mixed case, numbers, symbols and not compromised (breach check skipped in tests). |

In `config/sensible.php` you can also use the enum, e.g. `SetDefaultPassword::class => PasswordType::Pin` (`Datalogix\Sensible\Enums\PasswordType`). Any other value throws an exception.

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

`register()` accepts a class name (resolved through the container) or an instance. Classes that don't implement `Configurable` throw an `InvalidArgumentException`.

Configurables run once every service provider has booted, so registering them in `register()` or `boot()` both work. Configurables registered later are applied immediately, and registering the same class twice has no effect.

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

The key must be the configurable's fully qualified class name, so import the class in the config file; a missing or mismatched key silently disables it. `configValue()` returns the raw value, and `invalidConfigurationValue()` builds the exception for values you don't accept.

## License

MIT. See [LICENSE](LICENSE).
