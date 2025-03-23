<?php

namespace Stormmore\Framework\FluentReflection\Shared;

use ReflectionFunction;
use Stormmore\Framework\FluentReflection\Class\FluentClassParameters;

class FluentFunction implements IFluentParameterized
{
    private ReflectionFunction $function;
    public function __construct(callable $callable)
    {
        $this->function = new ReflectionFunction($callable);
    }

    public function invoke(array $args): mixed
    {
        return $this->function->invoke($args);
    }

    public function getParameters(): FluentClassParameters
    {
        return new FluentClassParameters($this->function->getParameters());
    }
}