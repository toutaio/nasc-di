<?php

declare(strict_types=1);

use Touta\Aria\Runtime\Failure;
use Touta\Aria\Runtime\Success;
use Touta\Nasc\Container;
use Touta\Nasc\ContainerError;
use Touta\Nasc\ServiceId;

// Scenario: resolve a bound factory via ServiceId
it('resolves a bound factory', function (): void {
    $container = Container::create()
        ->bind(new ServiceId('greeting'), fn(): string => 'hello');

    $result = $container->resolve(new ServiceId('greeting'));

    expect($result)->toBeInstanceOf(Success::class)
        ->and($result->value())->toBe('hello');
});

// Scenario: resolve returns ContainerError failure for unbound ServiceId
it('returns failure for unbound key', function (): void {
    $container = Container::create();

    $result = $container->resolve(new ServiceId('missing'));

    expect($result)->toBeInstanceOf(Failure::class)
        ->and($result->error())->toBeInstanceOf(ContainerError::class)
        ->and($result->error()->code)->toBe(ContainerError::NOT_FOUND);
});

// Scenario: resolve different bindings independently via ServiceId
it('resolves different bindings independently', function (): void {
    $container = Container::create()
        ->bind(new ServiceId('a'), fn(): int => 1)
        ->bind(new ServiceId('b'), fn(): int => 2);

    expect($container->resolve(new ServiceId('a'))->value())->toBe(1)
        ->and($container->resolve(new ServiceId('b'))->value())->toBe(2);
});

// Scenario: later binding overwrites earlier binding for same ServiceId
it('overwrites a previous binding', function (): void {
    $container = Container::create()
        ->bind(new ServiceId('val'), fn(): string => 'first')
        ->bind(new ServiceId('val'), fn(): string => 'second');

    expect($container->resolve(new ServiceId('val'))->value())->toBe('second');
});

// Scenario: non-singleton binding creates new instance on each resolve
it('creates a new instance on each resolve by default', function (): void {
    $container = Container::create()
        ->bind(new ServiceId('obj'), fn(): object => new stdClass());

    $a = $container->resolve(new ServiceId('obj'))->value();
    $b = $container->resolve(new ServiceId('obj'))->value();

    expect($a)->not->toBe($b);
});

// Scenario: singleton binding returns same instance on each resolve
it('returns the same instance for singleton bindings', function (): void {
    $container = Container::create()
        ->singleton(new ServiceId('obj'), fn(): object => new stdClass());

    $a = $container->resolve(new ServiceId('obj'))->value();
    $b = $container->resolve(new ServiceId('obj'))->value();

    expect($a)->toBe($b);
});

// Scenario: has checks binding existence via ServiceId
it('checks if a binding exists', function (): void {
    $container = Container::create()
        ->bind(new ServiceId('present'), fn(): int => 1);

    expect($container->has(new ServiceId('present')))->toBeTrue()
        ->and($container->has(new ServiceId('absent')))->toBeFalse();
});

// Scenario: container is immutable — bind returns a new container
it('is immutable - bind returns new container', function (): void {
    $original = Container::create();
    $withBinding = $original->bind(new ServiceId('x'), fn(): int => 1);

    expect($original->has(new ServiceId('x')))->toBeFalse()
        ->and($withBinding->has(new ServiceId('x')))->toBeTrue();
});
