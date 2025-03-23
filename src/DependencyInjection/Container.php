<?php

namespace Stormmore\Framework\DependencyInjection;

use ReflectionClass;

class Container
{
    private array $container = [];

    public function __get(string $name)
    {
        return $this->container[$name];
    }

    public function resolve(string $name): mixed
    {
        return $this->container[$name];
    }

    public function register(object $obj): void
    {
        $name = get_class($obj);
        $this->container[$name] = $obj;
    }

    public function registerAs(object $obj, string $name): void
    {
        $this->container[$name] = $obj;
    }

    public function isRegistered($key): bool
    {
        return array_key_exists($key, $this->container);
    }
}