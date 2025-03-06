<?php

namespace Stormmore\Framework\Classes\Parser;

class PhpClass
{
    /**
     * @var PhpClassMethod[]
     */
    public array $functions = [];

    public function __construct(public string $namespace, public string $name, public PhpAttributes $attributes)
    {
    }

    public function getFullyQualifiedName(): string
    {
        if ($this->namespace) {
            return $this->namespace . '\\' . $this->name;
        }
        return $this->name;
    }

    public function hasAttribute(string $className): bool
    {
        return $this->attributes->hasAttribute($className);
    }
}