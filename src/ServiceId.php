<?php

declare(strict_types=1);

namespace Touta\Nasc;

final readonly class ServiceId
{
    public function __construct(
        public string $value,
    ) {}
}
