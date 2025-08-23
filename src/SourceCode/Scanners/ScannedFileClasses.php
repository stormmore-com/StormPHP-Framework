<?php

namespace Stormmore\Framework\SourceCode\Scanners;

class ScannedFileClasses
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
        return array_map(function ($filePath) {
            return str_replace($this->sourceDirectory, '', $filePath);
        }, $this->classes);
    }

    public function getClasses(): array
    {
        return $this->classes;
    }
}