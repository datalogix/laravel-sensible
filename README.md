# Laravel Sensible

[![Latest Stable Version](https://poser.pugx.org/datalogix/laravel-sensible/version)](https://packagist.org/packages/datalogix/laravel-sensible)
[![Total Downloads](https://poser.pugx.org/datalogix/laravel-sensible/downloads)](https://packagist.org/packages/datalogix/laravel-sensible)
[![tests](https://github.com/datalogix/laravel-sensible/workflows/tests/badge.svg)](https://github.com/datalogix/laravel-sensible/actions)
[![codecov](https://codecov.io/gh/datalogix/laravel-sensible/branch/main/graph/badge.svg)](https://codecov.io/gh/datalogix/laravel-sensible)
[![License](https://poser.pugx.org/datalogix/laravel-sensible/license)](https://packagist.org/packages/datalogix/laravel-sensible)

> Laravel Sensible is a lightweight utility package for applying smart defaults and common best practices in everyday Laravel development.

## Installation

You can install the package via composer:

```bash
composer require datalogix/laravel-sensible
```

The package will automatically register itself.

## Features

All features are optional and fully configurable via `config/sensible.php`:

- 🚀 **Asset Prefetching** – Preload assets for faster load times.
- ⚡️ **Auto Eager Loading** – Avoid N+1 queries automatically.
- 😴 **Fake Sleep** – Mocks the delay function in tests, preventing real delays.
- 🔒 **Force HTTPS** – Enforce secure `https://` URLs.
- 🕒 **Immutable Dates** – Prevent unexpected date mutations.
- 🔄 **Prevent Stray Requests** – Block unmocked HTTP requests.
- 🛑 **Safe Console** – Block dangerous Artisan commands.
- 🔑 **Set Default Password** - Enforce strong password policies.
- ✅ **Strict Models** – Enforce strict model behavior.
- 🔓 **Optional Unguarded Models** – Disable mass-assignment protection.

## Configuration

You can publish the config file using the command:

```bash
php artisan vendor:publish --provider="Datalogix\Sensible\SensibleServiceProvider" --tag="config"
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

| Feature                 | Environment variable                              | Default                   |
| ----------------------- | ------------------------------------------------- | ------------------------- |
| Asset Prefetching       | `SENSIBLE_AGGRESSIVE_PREFETCHING`                 | `true`                    |
| Auto Eager Loading      | `SENSIBLE_AUTOMATICALLY_EAGER_LOAD_RELATIONSHIPS` | `true`                    |
| Fake Sleep (tests only) | `SENSIBLE_FAKE_SLEEP`                             | `true`                    |
| Force HTTPS             | `SENSIBLE_FORCE_SCHEME`                           | `true` in production      |
| Immutable Dates         | `SENSIBLE_IMMUTABLE_DATES`                        | `true`                    |
| Prevent Stray Requests  | `SENSIBLE_PREVENT_STRAY_REQUESTS`                 | `true`                    |
| Safe Console            | `SENSIBLE_PROHIBIT_DESTRUCTIVE_COMMANDS`          | `true` in production      |
| Set Default Password    | `SENSIBLE_SET_DEFAULT_PASSWORD`                   | `true` in production      |
| Strict Models           | `SENSIBLE_SHOULD_BE_STRICT`                       | `true` outside production |
| Unguarded Models        | `SENSIBLE_UNGUARD`                                | `false`                   |

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

## Custom configurables

You can register your own configurables by implementing `Datalogix\Sensible\Contracts\Configurable`:

```php
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

use Datalogix\Sensible\Sensible;

public function boot(): void
{
    Sensible::register(DefaultStringLength::class);
}
```

Configurables run once every service provider has booted, so registering them in `register()` or `boot()` both work. Configurables registered later are applied immediately, and registering the same class twice has no effect.
