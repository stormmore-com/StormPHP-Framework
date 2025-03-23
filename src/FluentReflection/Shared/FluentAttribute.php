<?php

namespace Stormmore\Framework\FluentReflection\Shared;

use ReflectionAttribute;

class FluentAttribute
{
    private ?object $instance;

    public function __construct(private ReflectionAttribute $attribute)
    {
        $this->instance = null;
    }

    public function getInstance(): object
    {
        if ($this->instance == null)
        {
            $this->instance = $this->attribute->newInstance();
        }
        return $this->instance;
    }
}