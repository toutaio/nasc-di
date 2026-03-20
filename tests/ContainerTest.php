<?php

declare(strict_types=1);

use Touta\Aria\Runtime\Failure;
use Touta\Aria\Runtime\Success;
use Touta\Nasc\Container;

it('resolves a bound factory', function (): void {
    $container = Container::create()
        ->bind('greeting', fn(): string => 'hello');

    $result = $container->resolve('greeting');

    expect($result)->toBeInstanceOf(Success::class)
        ->and($result->value())->toBe('hello');
});

it('returns failure for unbound key', function (): void {
    $container = Container::create();

    $result = $container->resolve('missing');

    expect($result)->toBeInstanceOf(Failure::class);
});

it('resolves different bindings independently', function (): void {
    $container = Container::create()
        ->bind('a', fn(): int => 1)
        ->bind('b', fn(): int => 2);

    expect($container->resolve('a')->value())->toBe(1)
        ->and($container->resolve('b')->value())->toBe(2);
});

it('overwrites a previous binding', function (): void {
    $container = Container::create()
        ->bind('val', fn(): string => 'first')
        ->bind('val', fn(): string => 'second');

    expect($container->resolve('val')->value())->toBe('second');
});

it('creates a new instance on each resolve by default', function (): void {
    $container = Container::create()
        ->bind('obj', fn(): object => new stdClass());

    $a = $container->resolve('obj')->value();
    $b = $container->resolve('obj')->value();

    expect($a)->not->toBe($b);
});

it('returns the same instance for singleton bindings', function (): void {
    $container = Container::create()
        ->singleton('obj', fn(): object => new stdClass());

    $a = $container->resolve('obj')->value();
    $b = $container->resolve('obj')->value();

    expect($a)->toBe($b);
});

it('checks if a binding exists', function (): void {
    $container = Container::create()
        ->bind('present', fn(): int => 1);

    expect($container->has('present'))->toBeTrue()
        ->and($container->has('absent'))->toBeFalse();
});

it('is immutable - bind returns new container', function (): void {
    $original = Container::create();
    $withBinding = $original->bind('x', fn(): int => 1);

    expect($original->has('x'))->toBeFalse()
        ->and($withBinding->has('x'))->toBeTrue();
});
