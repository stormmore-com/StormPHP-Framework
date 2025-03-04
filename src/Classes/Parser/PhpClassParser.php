<?php

namespace Stormmore\Framework\Classes\Parser;

use ArrayIterator;

class PhpClassParser
{
    public static function parse(ArrayIterator $it): PhpClass
    {
        $it->next();
        $it->next();
        $name = $it->current()->text;
        $class = new PhpClass();
        $class->name = $name;
        return $class;
    }
}