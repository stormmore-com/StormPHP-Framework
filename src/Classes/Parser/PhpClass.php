<?php

namespace Stormmore\Framework\Classes\Parser;

class PhpClass
{
    public string $namespace;
    public string $name;
    public array $attributes = [];

    public function getFullyQualifiedName(): string
    {
        if ($this->namespace) {
            return $this->namespace . '\\' . $this->name;
        }
        return $this->name;
    }
}