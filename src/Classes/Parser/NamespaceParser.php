<?php

namespace Stormmore\Framework\Classes\Parser;

use ArrayIterator;

class NamespaceParser
{
    public static function parse(ArrayIterator $it): string
    {
        $it->next();
        return $it->current()->text;
    }
}