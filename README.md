# Nasc DI

Dependency injection container with explicit, inspectable wiring for the Touta PHP ecosystem.

## Install

```bash
composer require touta/nasc-di
```

## Usage

```php
use Touta\Nasc\Container;

$container = Container::create()
    ->bind(LoggerInterface::class, fn() => new FileLogger('/tmp/app.log'))
    ->bind('db.host', fn() => 'localhost');

$logger = $container->resolve(LoggerInterface::class); // Success(FileLogger)
```

## License

MIT
