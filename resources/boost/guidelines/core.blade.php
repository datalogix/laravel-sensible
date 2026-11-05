## Laravel Sensible

This package applies framework defaults globally after the application boots, so they don't appear in application code. Each one is a "configurable", toggled in `config/sensible.php` (when published) or through its `SENSIBLE_*` environment variable. Check the application's config and environment before assuming the defaults below.

### Enabled by Default

- **Immutable Dates** (`SENSIBLE_IMMUTABLE_DATES`): `now()`, the `Date` facade and Eloquent date casts return `CarbonImmutable`. Always use the return value (`$date = $date->addDay()`) and type-hint `Carbon\CarbonInterface`, not `Illuminate\Support\Carbon`.
- **Auto Eager Loading** (`SENSIBLE_AUTOMATICALLY_EAGER_LOAD_RELATIONSHIPS`): a relationship lazy loaded on a model from a collection is loaded for the whole collection (Laravel 12.8+).
- **Asset Prefetching** (`SENSIBLE_AGGRESSIVE_PREFETCHING`): Vite assets are prefetched eagerly after the page loads.

### Enabled in Production

- **Force HTTPS** (`SENSIBLE_FORCE_SCHEME`): generated URLs use `https://`. Plain HTTP requests are not redirected.
- **Safe Console** (`SENSIBLE_PROHIBIT_DESTRUCTIVE_COMMANDS`): destructive database commands such as `migrate:fresh` are blocked.
- **Set Default Password** (`SENSIBLE_SET_DEFAULT_PASSWORD`): sets the rules returned by `Password::defaults()`, using the `complex` policy unless another one is set (`simple`, `numeric`, `pin`, `passphrase`, `alphanumeric`). Use `Password::defaults()` in validation; the policy doesn't apply to rules written by hand.

### Enabled Outside Production

- **Strict Models** (`SENSIBLE_SHOULD_BE_STRICT`): accessing a missing attribute, lazy loading a relationship that Auto Eager Loading didn't load, and mass assigning a non-fillable attribute all throw. Fix the cause (select the column, eager load the relationship, add it to `$fillable`) instead of disabling this. It is also enabled in tests.

### Enabled Only in Tests

- **Prevent Stray Requests** (`SENSIBLE_PREVENT_STRAY_REQUESTS`): an HTTP request without a matching fake throws. Fake every external call with `Http::fake()`.
- **Fake Sleep** (`SENSIBLE_FAKE_SLEEP`): the `Sleep` class doesn't pause. Use `Sleep::assertSlept()` to assert delays.

### Disabled by Default

- **Unguarded Models** (`SENSIBLE_UNGUARD`): disables mass-assignment protection globally. Don't enable it unless asked.

### Configuration Values

- Variables accept `true`/`false`, `1`/`0`, `on`/`off` and `yes`/`no`; any other value throws an exception.
- For the environment-dependent entries, leave the variable unset to keep the default. An empty value (`SENSIBLE_FORCE_SCHEME=`) disables them.
- Don't call `app()` in `config/sensible.php`: the published config is loaded before the environment is detected.

### Custom Configurables

Implement `Datalogix\Sensible\Contracts\Configurable` and register the class in a service provider. Use the `ReadsConfiguration` trait to read the entry under the class name in `config/sensible.php`.

@verbatim
<code-snippet name="Create and register a custom configurable" lang="php">
namespace App\Configurables;

use Datalogix\Sensible\Concerns\ReadsConfiguration;
use Datalogix\Sensible\Contracts\Configurable;
use Illuminate\Support\Facades\Schema;

class DefaultStringLength implements Configurable
{
    use ReadsConfiguration;

    public function enabled(): bool
    {
        return $this->configEnabled(default: true);
    }

    public function configure(): void
    {
        Schema::defaultStringLength(191);
    }
}

// In a service provider's boot() method:
\Datalogix\Sensible\Sensible::register(DefaultStringLength::class);
</code-snippet>
@endverbatim
