<?php

declare(strict_types=1);

namespace Touta\Nasc;

use Touta\Aria\Runtime\Failure;
use Touta\Aria\Runtime\Result;
use Touta\Aria\Runtime\StructuredFailure;
use Touta\Aria\Runtime\Success;

final class Container
{
    /** @var array<string, callable(): mixed> */
    private array $factories;

    /** @var array<string, true> */
    private array $singletons;

    /** @var array<string, mixed> */
    private array $resolved;

    /**
     * @param array<string, callable(): mixed> $factories
     * @param array<string, true> $singletons
     * @param array<string, mixed> $resolved
     */
    private function __construct(array $factories = [], array $singletons = [], array $resolved = [])
    {
        $this->factories = $factories;
        $this->singletons = $singletons;
        $this->resolved = $resolved;
    }

    public static function create(): self
    {
        return new self();
    }

    /**
     * @param callable(): mixed $factory
     */
    public function bind(string $id, callable $factory): self
    {
        $factories = $this->factories;
        $factories[$id] = $factory;
        $singletons = $this->singletons;
        unset($singletons[$id]);
        $resolved = $this->resolved;
        unset($resolved[$id]);

        return new self($factories, $singletons, $resolved);
    }

    /**
     * @param callable(): mixed $factory
     */
    public function singleton(string $id, callable $factory): self
    {
        $factories = $this->factories;
        $factories[$id] = $factory;
        $singletons = $this->singletons;
        $singletons[$id] = true;
        $resolved = $this->resolved;
        unset($resolved[$id]);

        return new self($factories, $singletons, $resolved);
    }

    /**
     * @return Success<mixed>|Failure<StructuredFailure>
     */
    public function resolve(string $id): Result
    {
        if (!isset($this->factories[$id])) {
            return Failure::from(new StructuredFailure(
                'BINDING_NOT_FOUND',
                "No binding registered for \"{$id}\"",
                ['id' => $id],
            ));
        }

        if (isset($this->singletons[$id]) && array_key_exists($id, $this->resolved)) {
            return Success::of($this->resolved[$id]);
        }

        $value = ($this->factories[$id])();

        if (isset($this->singletons[$id])) {
            $this->resolved[$id] = $value;
        }

        return Success::of($value);
    }

    public function has(string $id): bool
    {
        return isset($this->factories[$id]);
    }
}
