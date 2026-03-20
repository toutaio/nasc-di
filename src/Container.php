<?php

declare(strict_types=1);

namespace Touta\Nasc;

use Touta\Aria\Runtime\Failure;
use Touta\Aria\Runtime\Result;
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
    public function bind(ServiceId $id, callable $factory): self
    {
        $key = $id->value;
        $factories = $this->factories;
        $factories[$key] = $factory;
        $singletons = $this->singletons;
        unset($singletons[$key]);
        $resolved = $this->resolved;
        unset($resolved[$key]);

        return new self($factories, $singletons, $resolved);
    }

    /**
     * @param callable(): mixed $factory
     */
    public function singleton(ServiceId $id, callable $factory): self
    {
        $key = $id->value;
        $factories = $this->factories;
        $factories[$key] = $factory;
        $singletons = $this->singletons;
        $singletons[$key] = true;
        $resolved = $this->resolved;
        unset($resolved[$key]);

        return new self($factories, $singletons, $resolved);
    }

    /**
     * @return Success<mixed>|Failure<ContainerError>
     */
    public function resolve(ServiceId $id): Result
    {
        $key = $id->value;

        if (!isset($this->factories[$key])) {
            return Failure::from(new ContainerError(
                ContainerError::NOT_FOUND,
                "No binding registered for \"{$key}\"",
                ['id' => $key],
            ));
        }

        if (isset($this->singletons[$key]) && array_key_exists($key, $this->resolved)) {
            return Success::of($this->resolved[$key]);
        }

        $value = ($this->factories[$key])();

        if (isset($this->singletons[$key])) {
            $this->resolved[$key] = $value;
        }

        return Success::of($value);
    }

    public function has(ServiceId $id): bool
    {
        return isset($this->factories[$id->value]);
    }
}
