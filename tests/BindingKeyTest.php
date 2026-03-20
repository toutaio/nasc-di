<?php

declare(strict_types=1);

use Touta\Nasc\BindingKey;

// Scenario: branded type wraps a binding key string
it('wraps a binding key string', function (): void {
    $key = new BindingKey('logger.handler');

    expect($key->value)->toBe('logger.handler');
});

// Scenario: branded type preserves identity equality
it('preserves identity equality for same value', function (): void {
    $a = new BindingKey('logger.handler');
    $b = new BindingKey('logger.handler');

    expect($a)->toEqual($b);
});

// Scenario: branded type distinguishes different keys
it('distinguishes different keys', function (): void {
    $a = new BindingKey('logger.handler');
    $b = new BindingKey('cache.adapter');

    expect($a)->not->toEqual($b);
});
