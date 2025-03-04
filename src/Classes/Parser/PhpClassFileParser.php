<?php

namespace Stormmore\Framework\Classes\Parser;

use ArrayIterator;
use PhpToken;

class PhpClassFileParser
{
    public static function parse(string $filePath): array
    {
        $tokens = PhpToken::tokenize(file_get_contents($filePath));
        $it = new ArrayIterator($tokens);

        $attributes = [];
        $classes = [];
        $namespace = '';
        while($it->valid()) {
            $token = $it->current();
            if ($token->text == 'namespace') {
                $namespace = NamespaceParser::parse($it);
            }
            if ($token->text == '#[') {
                $attributes[] = AttributeParser::parse($it);
            }
            if ($token->text == 'class') {
                $class = PhpClassParser::parse($it);
                $class->attributes = $attributes;
                $class->namespace = $namespace;
                $classes[] = $class;
                $attributes = [];
            }
            $it->next();
        }

        return $classes;
    }
}