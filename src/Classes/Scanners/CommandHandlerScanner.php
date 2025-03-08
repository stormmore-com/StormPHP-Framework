<?php

namespace Stormmore\Framework\Classes\Scanners;

use Stormmore\Framework\Classes\Parser\Models\PhpClass;
use Stormmore\Framework\Classes\Parser\PhpClassFileParser;
use Stormmore\Framework\Cqs\CommandHandler;

class CommandHandlerScanner
{
    public function scan(array $classes): array
    {
        $handlers = [];
        foreach ($classes as $filePath) {
            $classes = PhpClassFileParser::parse($filePath);
            foreach ($classes as $class) {
                if ($class->hasAttribute(CommandHandler::class)) {
                    $handlers[$class->getFullyQualifiedName()] = $this->getCommandName($class);
                }
            }
        }
        return $handlers;
    }

    private function getCommandName(PhpClass $class): string
    {
        $className = $class->getAttribute(CommandHandler::class)->args;
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