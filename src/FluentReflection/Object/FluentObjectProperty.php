<?php

namespace Stormmore\Framework\FluentReflection\Object;

use ReflectionProperty;
use Stormmore\Framework\FluentReflection\Shared\SafeValue;
use Stormmore\Framework\FluentReflection\Shared\FluentType;

class FluentObjectProperty
{
    public FluentType $type;

    public function __construct(private ReflectionProperty $reflection, private object $object)
    {
        $this->type = new FluentType($this->reflection);
    }

    public function setValue(mixed $value): void
    {
        $this->reflection->setValue($this->object, $value);
    }

    public function cast(mixed $value): SafeValue
    {
        $valueType = gettype($value);
        return new SafeValue();
    }
}