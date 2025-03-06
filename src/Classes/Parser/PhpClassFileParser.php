<?php

namespace Stormmore\Framework\Classes\Parser;

use ArrayIterator;
use PhpToken;

class PhpClassFileParser
{
    /**
     * @param string $filePath
     * @return  PhpClass[]
     */
    public static function parse(string $filePath): array
    {
        $tokens = PhpToken::tokenize(file_get_contents($filePath));
        $tokens = array_filter($tokens, function ($token) { return $token->getTokenName() !== 'T_WHITESPACE'; });
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
                $class = PhpClassParser::parse($it, $namespace, new PhpAttributes($attributes));
                $classes[] = $class;
                $attributes = [];
            }
            $it->next();
        }

        return $classes;
    }
}