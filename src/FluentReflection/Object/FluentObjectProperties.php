<?php

namespace Stormmore\Framework\FluentReflection\Object;

use ReflectionObject;

readonly class FluentObjectProperties
{
    public function __construct(private ReflectionObject $reflection, private object $object)
    {
    }

    public function exist(string $name): bool
    {
        return $this->reflection->hasProperty($name);
    }

    public function get(string $name): FluentObjectProperty
    {
        return new FluentObjectProperty($this->reflection->getProperty($name), $this->object);
    }
}