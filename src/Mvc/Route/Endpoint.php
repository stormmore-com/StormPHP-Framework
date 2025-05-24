<?php

namespace Stormmore\Framework\Mvc\Route;

use closure;

readonly class Endpoint
{
    public function __construct(private closure|array|string $endpoint)
    {
    }

    public function isController(): bool
    {
        return is_array($this->endpoint);
    }

    public function isCallable(): bool
    {
        return is_callable($this->endpoint);
    }

    public function isScript(): bool
    {
        return is_string($this->endpoint);
    }

    public function getCallable(): callable
    {
        return $this->endpoint;
    }

    public function getScriptPath(): string
    {
        return $this->endpoint;
    }

    public function getControllerActionList(): array
    {
        return $this->endpoint;
    }

    public function hasControllerReflection(): bool
    {
       return class_exists($this->endpoint[0]) and method_exists($this->endpoint[0], $this->endpoint[1]);
    }
}