<?php

namespace Stormmore\Framework\FluentReflection;

use stdClass;
use ReflectionObject;

readonly class ObjectPropertyAccess
{
    private object $object;
    private ReflectionObject $reflection;

    public static function create(object $object): ObjectPropertyAccess
    {
        return new ObjectPropertyAccess($object);
    }

    public function __construct(object $object)
    {
        $this->object = $object;
        $this->reflection = new ReflectionObject($object);
    }

    public function set(string $name, mixed $value): bool
    {
        $methodName = 'set' . $name;
        if ($this->reflection->hasMethod($methodName)) {
            $method = $this->reflection->getMethod($methodName);
            $method->invoke($this->object, $value);
            return true;
        } else if ($this->reflection->hasProperty($name)) {
            $property = $this->reflection->getProperty($name);
            $access = new ReflectionPropertyAccess($this->object, $property);
            return $access->set($value);
        }
        else if ($this->object instanceof stdClass) {
            $this->object->$name = $value;
            return true;
        }

        return false;
    }
}