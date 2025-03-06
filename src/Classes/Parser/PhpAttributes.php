<?php

namespace Stormmore\Framework\Classes\Parser;

use ArrayObject;

class PhpAttributes extends ArrayObject
{
    public function __construct(private readonly array $attributes)
    {
        parent::__construct($this->attributes);
    }

    public function hasAttribute(string $className): bool
    {
        $items =  explode("\\", $className);
        $name = end($items);
        $search = [
            $name,
            $className
        ];
        foreach($this->attributes as $attribute) {
            if (in_array($attribute->name, $search)) {
                return true;
            }
        }
        return false;
    }
}