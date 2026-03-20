<?php

declare(strict_types=1);

namespace Touta\Nasc;

final readonly class BindingKey
{
    public function __construct(
        public string $value,
    ) {}
}
