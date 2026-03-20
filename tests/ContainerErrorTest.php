<?php

declare(strict_types=1);

use Touta\Nasc\ContainerError;

// Scenario: domain error holds code and message
it('holds code and message', function (): void {
    $error = new ContainerError(ContainerError::NOT_FOUND, 'Service not found');

    expect($error->code)->toBe('CONTAINER.NOT_FOUND')
        ->and($error->message)->toBe('Service not found')
        ->and($error->context)->toBe([]);
});

// Scenario: domain error holds context
it('holds context', function (): void {
    $error = new ContainerError(
        ContainerError::CIRCULAR_DEPENDENCY,
        'Circular detected',
        ['chain' => ['a', 'b', 'a']],
    );

    expect($error->context)->toBe(['chain' => ['a', 'b', 'a']]);
});

// Scenario: withMessage returns new instance with updated message
it('returns new instance with updated message via withMessage', function (): void {
    $original = new ContainerError(ContainerError::NOT_FOUND, 'original');
    $updated = $original->withMessage('updated');

    expect($updated->message)->toBe('updated')
        ->and($updated->code)->toBe(ContainerError::NOT_FOUND)
        ->and($original->message)->toBe('original');
});

// Scenario: withContext merges additional context
it('merges additional context via withContext', function (): void {
    $original = new ContainerError(ContainerError::BINDING_FAILED, 'fail', ['id' => 'x']);
    $updated = $original->withContext(['reason' => 'invalid']);

    expect($updated->context)->toBe(['id' => 'x', 'reason' => 'invalid'])
        ->and($original->context)->toBe(['id' => 'x']);
});

// Scenario: error constants have correct values
it('defines correct error code constants', function (): void {
    expect(ContainerError::NOT_FOUND)->toBe('CONTAINER.NOT_FOUND')
        ->and(ContainerError::CIRCULAR_DEPENDENCY)->toBe('CONTAINER.CIRCULAR_DEPENDENCY')
        ->and(ContainerError::BINDING_FAILED)->toBe('CONTAINER.BINDING_FAILED');
});
