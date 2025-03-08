<?php

namespace Stormmore\Framework\Classes\Parser\Models;

class PhpClass
{
    /**
     * @var PhpClassMethod[]
     */
    public array $functions = [];

    /**
     * @param string $namespace
     * @param PhpUse[] $uses
     * @param string $name
     * @param PhpAttributes $attributes
     */
    public function __construct(public string $namespace, public array $uses, public string $name, public PhpAttributes $attributes)
    {
    }

    public function getFullyQualifiedName(): string
    {
        if ($this->namespace) {
            return $this->namespace . '\\' . $this->name;
        }
        return $this->name;
    }

    public function getAttribute(string $name): PhpAttribute
    {
        return $this->attributes->getAttribute($name);
    }

    public function hasAttribute(string $className): bool
    {
        return $this->attributes->hasAttribute($className);
    }
}