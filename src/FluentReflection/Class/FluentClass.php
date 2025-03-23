<?php

namespace Stormmore\Framework\FluentReflection\Class;

use ReflectionClass;
use Stormmore\Framework\FluentReflection\Shared\FluentAttributes;

readonly class FluentClass
{
    private ReflectionClass $class;

    public FluentClassProperties $properties;

    public FluentClassMethods $methods;

    private function __construct(private string $className)
    {
        $this->class = new ReflectionClass($this->className);
        $this->methods = new FluentClassMethods($this->class);
        $this->properties = new FluentClassProperties($this->class);
    }

    public static function create(string $className): FluentClass
    {
        return new FluentClass($className);
    }

    public static function classExists(string $className): bool
    {
        return class_exists($className);
    }

    public function createInstance(array $args): object
    {
        return $this->class->newInstanceArgs($args);
    }

    public function hasConstructor(): bool
    {
        return $this->class->getConstructor() !== null;
    }

    public function getConstructor(): FluentClassMethod
    {
        return new FluentClassMethod($this->class->getConstructor());
    }

    public function hasAttribute(string $name): bool
    {
        return count($this->class->getAttributes($name)) > 0;
    }

    /**
     * @param string $name
     * @return FluentAttributes
     */
    public function getAttributes(string $name): FluentAttributes
    {
        return new FluentAttributes($this->class->getAttributes($name));
    }
}