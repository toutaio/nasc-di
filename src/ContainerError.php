<?php

declare(strict_types=1);

namespace Touta\Nasc;

final readonly class ContainerError
{
    public const NOT_FOUND = 'CONTAINER.NOT_FOUND';
    public const CIRCULAR_DEPENDENCY = 'CONTAINER.CIRCULAR_DEPENDENCY';
    public const BINDING_FAILED = 'CONTAINER.BINDING_FAILED';

    public function __construct(
        public string $code,
        public string $message,
        /** @var array<string, mixed> */
        public array $context = [],
    ) {}

    public function withMessage(string $message): self
    {
        return new self($this->code, $message, $this->context);
    }

    /** @param array<string, mixed> $context */
    public function withContext(array $context): self
    {
        return new self($this->code, $this->message, array_merge($this->context, $context));
    }
}
