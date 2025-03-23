<?php

namespace Stormmore\Framework\FluentReflection\Class;

use ReflectionClass;

class FluentClassMethods
{
    private array $methods = [];

    public function __construct(private ReflectionClass $class)
    {
    }

    public function getMethod(string $name): ?FluentClassMethod
    {
        if (array_key_exists($name, $this->methods)) {
            return $this->methods[$name];
        }
        else if ($this->class->hasMethod($name)) {
            $fluentMethod = new FluentClassMethod($this->class->getMethod($name));
            $this->methods[$name] = $fluentMethod;
            return $fluentMethod;
        }
        return null;
    }
}