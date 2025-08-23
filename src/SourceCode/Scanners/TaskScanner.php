<?php

namespace Stormmore\Framework\SourceCode\Scanners;

use Stormmore\Framework\Cli\CliTask;
use Stormmore\Framework\SourceCode\Parser\Models\PhpClass;
use Stormmore\Framework\SourceCode\Parser\PhpClassFileParser;

class TaskScanner
{
    public function scan(ScannedFileClasses $fileClassCollection): array
    {
        $tasks = [];
        foreach ($fileClassCollection->getClasses() as $filePath) {
            $classes = PhpClassFileParser::parse($filePath);
            foreach ($classes as $class) {
                if ($class->hasAttribute(CliTask::class)) {
                    $tasks[$this->getTaskName($class)] = $class->getFullyQualifiedName();
                }
            }
        }
        return $tasks;
    }

    private function getTaskName(PhpClass $class): string
    {
        $className = $class->getAttribute(CliTask::class)->args;
        return str_replace(array('"', "'"),  '', $className);
    }
}