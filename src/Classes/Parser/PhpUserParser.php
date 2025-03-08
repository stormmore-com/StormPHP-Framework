<?php

namespace Stormmore\Framework\Classes\Parser;

use ArrayIterator;
use Stormmore\Framework\Classes\Parser\Models\PhpUse;

class PhpUserParser
{
    public static function parse(ArrayIterator $it): PhpUse
    {
        $it->next();
        $as = "";
        $fullyQualifiedName = $it->current()->text;

        $it->next();
        if ($it->current()->text === 'as') {
            $it->next();
            $as = $it->current()->text;
        }
        else {
            $it->seek($it->key() - 1);
        }
        return new PhpUse($fullyQualifiedName, $as);
    }
}