<?php

declare(strict_types=1);

use Touta\Nasc\ServiceId;

// Scenario: branded type wraps a service identifier string
it('wraps a service identifier string', function (): void {
    $id = new ServiceId('app.logger');

    expect($id->value)->toBe('app.logger');
});

// Scenario: branded type preserves identity equality
it('preserves identity equality for same value', function (): void {
    $a = new ServiceId('app.logger');
    $b = new ServiceId('app.logger');

    expect($a)->toEqual($b);
});

// Scenario: branded type distinguishes different identifiers
it('distinguishes different identifiers', function (): void {
    $a = new ServiceId('app.logger');
    $b = new ServiceId('app.cache');

    expect($a)->not->toEqual($b);
});
