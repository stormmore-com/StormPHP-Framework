<?php

namespace Stormmore\Framework\Reflection;

readonly class ObjectReflector
{
    private object $object;
    private ObjectReflector $reflection;

    public static function create(object $object): ObjectReflector
    {
        return new ObjectReflector($object);
    }

    public function __construct(object $object)
    {
        $this->object = $object;
        $this->reflection = new ReflectionObject($object);
    }

    public function set(string $name, mixed $value): void
    {
        $methodName = 'set' . $name;
        if ($this->reflection->hasMethod($methodName)) {
            $method = $this->reflection->getMethod($methodName);
            $method->invoke($this->object, $value);
        } else if ($this->reflection->hasProperty($name) or $this->object instanceof stdClass) {
            $this->object->$name = $value;
        }
    }
}