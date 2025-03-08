<?php

namespace Stormmore\Framework\Classes\Parser;

use ArrayIterator;
use PhpToken;
use Stormmore\Framework\Classes\Parser\Models\PhpAttributes;
use Stormmore\Framework\Classes\Parser\Models\PhpClass;

class PhpClassFileParser
{
    /**
     * @param string $filePath
     * @return  PhpClass[]
     */
    public static function parse(string $filePath): array
    {
        $tokens = PhpToken::tokenize(file_get_contents($filePath));
        $tokens = array_values(array_filter($tokens, function ($token) { return $token->getTokenName() !== 'T_WHITESPACE'; }));
        $it = new ArrayIterator($tokens);

        $attributes = [];
        $uses = [];
        $classes = [];
        $namespace = '';
        while($it->valid()) {
            $token = $it->current();
            if ($token->text == 'use') {
                $uses[] = PhpUserParser::parse($it);
            }
            if ($token->text == 'namespace') {
                $namespace = NamespaceParser::parse($it);
            }
            if ($token->text == '#[') {
                $attributes[] = AttributeParser::parse($it);
            }
            if ($token->text == 'class') {
                $class = PhpClassParser::parse($it, $namespace, $uses, new PhpAttributes($attributes));
                $classes[] = $class;
                $attributes = [];
            }
            $it->next();
        }

        return $classes;
    }
}