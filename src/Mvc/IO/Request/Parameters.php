<?php

namespace Stormmore\Framework\Mvc\IO\Request;

class Parameters implements IParameters
{
    private array $parameters = [];

    public function __construct(array ...$parameters)
    {
        foreach ($parameters as $parameter) {
            $this->parameters = array_merge($parameter);
        }
    }

    public function has(string $name): bool
    {
        return array_key_exists($name, $this->parameters);
    }

    public function get(string $name): mixed
    {
        return $this->parameters[$name] ?? null;
    }

    public function toArray(): array
    {
        return $this->parameters;
    }
}