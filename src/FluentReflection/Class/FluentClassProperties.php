<?php

namespace Stormmore\Framework\FluentReflection\Class;

use Exception;
use ReflectionClass;
use Stormmore\Framework\FluentReflection\Shared\IFluentProperties;
use Stormmore\Framework\FluentReflection\Shared\IFluentProperty;

class FluentClassProperties
{
    public function __construct(private ReflectionClass $class)
    {
    }

    public function get(string $name)
    {
        throw new Exception("Not implemented yet");
    }
}