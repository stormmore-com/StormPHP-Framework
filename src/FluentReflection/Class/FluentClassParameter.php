<?php

namespace Stormmore\Framework\FluentReflection\Class;

use ReflectionParameter;
use Stormmore\Framework\FluentReflection\Shared\FluentType;
use Stormmore\Framework\FluentReflection\Shared\IFluentParameter;

class FluentClassParameter implements IFluentParameter
{
    public FluentType $type;

    public function __construct(private readonly ReflectionParameter $parameter)
    {
        $this->type = new FluentType($this->parameter);
    }

    public function isNullable(): bool
    {
        return $this->parameter->allowsNull();
    }

    public function getName(): string
    {
        return $this->parameter->getName();
    }
}