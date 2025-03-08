<?php

namespace Stormmore\Framework\Classes\Parser\Models;

use ArrayObject;

class PhpAttributes extends ArrayObject
{
    public function __construct(private readonly array $attributes)
    {
        parent::__construct($this->attributes);
    }

    public function getAttribute(string $className): null|PhpAttribute
    {
        $items =  explode("\\", $className);
        $name = end($items);
        $search = [
            $name,
            $className
        ];
        foreach($this->attributes as $attribute) {
            if (in_array($attribute->name, $search)) {
                return $attribute;
            }
        }
        return null;
    }

    public function hasAttribute(string $className): bool
    {
        return $this->getAttribute($className) !== null;
    }
}