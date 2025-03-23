<?php

namespace Stormmore\Framework\FluentReflection;

use ReflectionProperty;

class ReflectionPropertyAccess
{
    public function __construct(private object $object, private ReflectionProperty $property)
    {
    }

    public function set(mixed $value): bool
    {
        if ($this->property->isPublic()) {
            if ($this->property->hasType()) {
                foreach(ReflectionTypeGetter::getTypes($this->property) as $type) {
                    $valueType = gettype($value);
                }
            }
            else {
                $this->property->setValue($this->object, $value);
                return true;
            }
        }
        return false;
    }
}