<?php

namespace Stormmore\Framework\SourceCode\Scanners;

use Stormmore\Framework\SourceCode\Parser\Models\PhpClass;
use Stormmore\Framework\SourceCode\Parser\PhpClassFileParser;

readonly class HandlerScanner
{
    public function __construct(private string $attributeName)
    {
    }

    public function scan(array $classes): array
    {
        $handlers = [];
        foreach ($classes as $filePath) {
            $classes = PhpClassFileParser::parse($filePath);
            foreach ($classes as $class) {
                if ($class->hasAttribute($this->attributeName)) {
                    $handlers[$class->getFullyQualifiedName()] = $this->getHandlerName($class);
                }
            }
        }
        return $handlers;
    }

    private function getHandlerName(PhpClass $class): string
    {
        $className = $class->getAttribute($this->attributeName)->args;
        $className = str_replace(array('"', "'", '::class'), '', $className);
        if (!str_contains($className, "\\")) {
            foreach ($class->uses as $use) {
                if ($use->is($className)) {
                    return $use->fullyQualifiedName;
                }
            }
            if ($class->namespace) {
                return $class->namespace . "\\" . $className;
            }
        }
        return $className;
    }
}