<?php

namespace Stormmore\Framework\Classes\Parser;

use ArrayIterator;

class AttributeParser
{
    public static function parse(ArrayIterator $it): PhpAttribute
    {
        $it->next();
        $token = $it->current();
        $name = $token->text;

        $args = '';
        $it->next();
        while($it->current()->text !== ']') {
            $token = $it->current();
            $text = $token->text;
            if ($text != '(' and $text != ')') {
                $args .= $text;
            }
            $it->next();
        }

        return new PhpAttribute($name, $args);
    }
}