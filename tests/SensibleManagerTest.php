<?php

use Datalogix\Sensible\Contracts\Configurable;
use Datalogix\Sensible\SensibleManager;
use Datalogix\Sensible\Tests\Fixtures\TestConfigurable;

test('register', function () {
    $mock = $this->mock(TestConfigurable::class);
    $mock->shouldReceive('enabled')->once()->andReturn(true);
    $mock->shouldReceive('configure')->once();

    $sensibleManager = new SensibleManager;
    $sensibleManager->register(TestConfigurable::class);
    $sensibleManager->run();
});

test('register invalid configurable', function () {
    $sensibleManager = new SensibleManager;
    $sensibleManager->register('Foo');
})->throws(InvalidArgumentException::class, 'Foo must implement '.Configurable::class);

test('register ignores duplicates', function () {
    $mock = $this->mock(TestConfigurable::class);
    $mock->shouldReceive('enabled')->once()->andReturn(true);
    $mock->shouldReceive('configure')->once();

    $sensibleManager = new SensibleManager;
    $sensibleManager->register(TestConfigurable::class);
    $sensibleManager->register(TestConfigurable::class);
    $sensibleManager->run();
});

test('register ignores a duplicate with a leading backslash', function () {
    $mock = $this->mock(TestConfigurable::class);
    $mock->shouldReceive('enabled')->once()->andReturn(true);
    $mock->shouldReceive('configure')->once();

    $sensibleManager = new SensibleManager;
    $sensibleManager->register(TestConfigurable::class);
    $sensibleManager->register('\\'.TestConfigurable::class);
    $sensibleManager->run();
});

test('register replaces an already registered class with the given instance', function () {
    $mock = $this->mock(TestConfigurable::class);
    $mock->shouldNotReceive('enabled');
    $mock->shouldNotReceive('configure');

    $instance = new TestConfigurable;

    $sensibleManager = new SensibleManager;
    $sensibleManager->register(TestConfigurable::class);
    $sensibleManager->register($instance);
    $sensibleManager->run();

    expect($instance->configured)->toBeTrue();
});

test('register ignores a duplicate with a different case', function () {
    $mock = $this->mock(TestConfigurable::class);
    $mock->shouldReceive('enabled')->once()->andReturn(true);
    $mock->shouldReceive('configure')->once();

    app()->instance(strtolower(TestConfigurable::class), $mock);

    $sensibleManager = new SensibleManager;
    $sensibleManager->register(TestConfigurable::class);
    $sensibleManager->register(strtolower(TestConfigurable::class));
    $sensibleManager->run();
});

test('register replaces a class registered with a different case', function () {
    $mock = $this->mock(TestConfigurable::class);
    $mock->shouldNotReceive('enabled');
    app()->instance(strtolower(TestConfigurable::class), $mock);

    $instance = new TestConfigurable;

    $sensibleManager = new SensibleManager;
    $sensibleManager->register(strtolower(TestConfigurable::class));
    $sensibleManager->register($instance);
    $sensibleManager->run();

    expect($instance->configured)->toBeTrue();
});

test('register ignores a class name already registered as an instance', function () {
    $mock = $this->mock(TestConfigurable::class);
    $mock->shouldNotReceive('enabled');

    $instance = new TestConfigurable;

    $sensibleManager = new SensibleManager;
    $sensibleManager->register($instance);
    $sensibleManager->register(TestConfigurable::class);
    $sensibleManager->run();

    expect($instance->configured)->toBeTrue();
});

test('register an instance after run applies it immediately', function () {
    $instance = new TestConfigurable;

    $sensibleManager = new SensibleManager;
    $sensibleManager->register(TestConfigurable::class);
    $sensibleManager->run();
    $sensibleManager->register($instance);

    expect($instance->configured)->toBeTrue();
});

test('register replaces an instance with a later instance of the same class', function () {
    $first = new TestConfigurable;
    $second = new TestConfigurable;

    $sensibleManager = new SensibleManager;
    $sensibleManager->register($first);
    $sensibleManager->register($second);
    $sensibleManager->run();

    expect($first->configured)->toBeFalse()
        ->and($second->configured)->toBeTrue();
});

test('register the same instance after run does not configure it again', function () {
    $instance = new TestConfigurable;

    $sensibleManager = new SensibleManager;
    $sensibleManager->register($instance);
    $sensibleManager->run();

    $instance->configured = false;
    $sensibleManager->register($instance);

    expect($instance->configured)->toBeFalse();
});

test('register after run applies immediately', function () {
    $sensibleManager = new SensibleManager;
    $sensibleManager->run();

    $mock = $this->mock(TestConfigurable::class);
    $mock->shouldReceive('enabled')->once()->andReturn(true);
    $mock->shouldReceive('configure')->once();

    $sensibleManager->register(TestConfigurable::class);
});

test('register uses the given instance', function () {
    $configurable = new TestConfigurable;

    $sensibleManager = new SensibleManager;
    $sensibleManager->register($configurable);
    $sensibleManager->run();

    expect($configurable->configured)->toBeTrue();
});

test('run skips disabled configurables', function () {
    $mock = $this->mock(TestConfigurable::class);
    $mock->shouldReceive('enabled')->once()->andReturn(false);
    $mock->shouldNotReceive('configure');

    $sensibleManager = new SensibleManager;
    $sensibleManager->register(TestConfigurable::class);
    $sensibleManager->run();
});

test('run throws when the container does not resolve a configurable', function () {
    app()->instance(TestConfigurable::class, new stdClass);

    $sensibleManager = new SensibleManager;
    $sensibleManager->register(TestConfigurable::class);
    $sensibleManager->run();
})->throws(InvalidArgumentException::class, TestConfigurable::class.' must resolve to an instance of '.Configurable::class);

test('registering a non-canonical class name uses the container binding', function (string $name) {
    $bound = new class extends TestConfigurable {};
    app()->instance(TestConfigurable::class, $bound);

    $sensibleManager = new SensibleManager;
    $sensibleManager->register($name);
    $sensibleManager->run();

    expect($bound->configured)->toBeTrue();
})->with([
    'leading backslash' => '\\'.TestConfigurable::class,
    'different case' => strtolower(TestConfigurable::class),
]);

test('run applies every built-in configurable', function () {
    foreach (glob(__DIR__.'/../src/Configurables/*.php') ?: [] as $file) {
        $mock = $this->mock('Datalogix\\Sensible\\Configurables\\'.basename($file, '.php'));
        $mock->shouldReceive('enabled')->once()->andReturn(false);
    }

    (new SensibleManager)->run();
});

test('register adds an instance of a subclass alongside the parent', function () {
    $parent = new TestConfigurable;
    $child = new class extends TestConfigurable {};

    $sensibleManager = new SensibleManager;
    $sensibleManager->register($parent);
    $sensibleManager->register($child);
    $sensibleManager->run();

    expect($parent->configured)->toBeTrue()
        ->and($child->configured)->toBeTrue();
});

test('register during run applies immediately', function () {
    $sensibleManager = new SensibleManager;
    $instance = new TestConfigurable;

    $sensibleManager->register(new class($sensibleManager, $instance) implements Configurable
    {
        public function __construct(private SensibleManager $manager, private TestConfigurable $instance) {}

        public function enabled(): bool
        {
            return true;
        }

        public function configure(): void
        {
            $this->manager->register($this->instance);
        }
    });
    $sensibleManager->run();

    expect($instance->configured)->toBeTrue();
});

test('a registration that fails after run can be retried', function () {
    $sensibleManager = new SensibleManager;
    $sensibleManager->run();

    app()->bind(TestConfigurable::class, fn () => new stdClass);

    expect(fn () => $sensibleManager->register(TestConfigurable::class))
        ->toThrow(InvalidArgumentException::class);

    $instance = new TestConfigurable;
    app()->instance(TestConfigurable::class, $instance);

    $sensibleManager->register(TestConfigurable::class);

    expect($instance->configured)->toBeTrue();
});

test('a failed registration after run keeps what it registered', function () {
    $sensibleManager = new SensibleManager;
    $sensibleManager->run();

    $nested = new TestConfigurable;

    $failing = new class($sensibleManager, $nested) implements Configurable
    {
        public function __construct(private SensibleManager $manager, private TestConfigurable $nested) {}

        public function enabled(): bool
        {
            return true;
        }

        public function configure(): void
        {
            $this->manager->register($this->nested);

            throw new RuntimeException('Failed.');
        }
    };

    expect(fn () => $sensibleManager->register($failing))->toThrow(RuntimeException::class);

    $nested->configured = false;
    $sensibleManager->register($nested);

    expect($nested->configured)->toBeFalse();
});

test('a failed replacement after run keeps the previous instance', function () {
    $make = fn (bool $fails) => new class($fails) implements Configurable
    {
        public int $hits = 0;

        public function __construct(private bool $fails) {}

        public function enabled(): bool
        {
            return true;
        }

        public function configure(): void
        {
            throw_if($this->fails, RuntimeException::class, 'Failed.');

            $this->hits++;
        }
    };

    $previous = $make(false);

    $sensibleManager = new SensibleManager;
    $sensibleManager->register($previous);
    $sensibleManager->run();

    expect(fn () => $sensibleManager->register($make(true)))->toThrow(RuntimeException::class);

    $sensibleManager->run();

    expect($previous->hits)->toBe(2);
});
