<?php

namespace Stormmore\Framework\Classes;

use PhpToken;
use Stormmore\Framework\Classes\Parser\PhpClassFileParser;

class CommandHandlerScanner
{
    public function scan(array $classes): array
    {
        $handlers = [];
        foreach ($classes as $filePath) {
            $classes = PhpClassFileParser::parse($filePath);
        }
        return $handlers;
    }

}