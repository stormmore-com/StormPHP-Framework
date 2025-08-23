<?php

namespace Stormmore\Framework\SourceCode\Scanners;

class FileClassCollection
{
    private array $classes = [];

    public function __construct(private string $sourceDirectory)
    {
    }

    public function addClass(string $className, string $filePath): void
    {
        $this->classes[$className] = $filePath;
    }

    public function getClassesWithRelativePaths(): array
    {
        return $this->classes;
    }

    public function getClasses(): array
    {
        return $this->classes;
    }
}