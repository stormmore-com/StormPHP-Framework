<?php

namespace Stormmore\Framework\FluentReflection\Class;

use ReflectionMethod;
use Stormmore\Framework\FluentReflection\Shared\FluentAttributes;
use Stormmore\Framework\FluentReflection\Shared\IFluentParameterized;

readonly class FluentClassMethod implements IFluentParameterized
{
    public function __construct(private ReflectionMethod $method)
    {
    }

    public function getName(): string
    {
        return $this->method->getName();
    }

    public function hasAttribute(string $name): bool
    {
        return count($this->method->getAttributes($name)) > 0;
    }

    /**
     * @param string $name
     * @return FluentAttributes
     */
    public function getAttributes(string $name): FluentAttributes
    {
        return new FluentAttributes($this->method->getAttributes($name));
    }

    public function invoke(object $object, array $args): mixed
    {
        return $this->method->invokeArgs($object, $args);
    }

    public function getParameters(): FluentClassParameters
    {
        return new FluentClassParameters($this->method->getParameters());
    }
}