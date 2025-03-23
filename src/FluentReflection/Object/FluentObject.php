<?php

namespace Stormmore\Framework\FluentReflection\Object;

use ReflectionObject;
use Stormmore\Framework\FluentReflection\Shared\FluentAttributes;

class FluentObject
{
    private object $object;
    private ReflectionObject $reflection;
    public FluentObjectProperties $properties;

    public function __construct(object $object)
    {
        $this->object = $object;
        $this->reflection = new ReflectionObject($this->object);
        $this->properties = new FluentObjectProperties($this->reflection, $this->object);
    }

    public function hasAttribute(string $name): bool
    {
        return count($this->reflection->getAttributes($name)) > 0;
    }

    /**
     * @param string $name
     * @return FluentAttributes
     */
    public function getAttributes(string $name): FluentAttributes
    {
        return new FluentAttributes($this->reflection->getAttributes($name));
    }


}