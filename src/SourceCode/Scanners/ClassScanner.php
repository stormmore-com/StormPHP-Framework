<?php

namespace Stormmore\Framework\SourceCode\Scanners;

use Exception;
use Stormmore\Framework\SourceCode\Parser\PhpClassFileParser;

readonly class ClassScanner
{
    function __construct(private string $sourceDirectory) { }

    /**
     * @throws Exception
     */
    public function scan(): FileClassCollection
    {
        $classes = new FileClassCollection($this->sourceDirectory);
        foreach ($this->getPhpFiles() as $phpFilePath) {
            $fileClasses = PhpClassFileParser::parse($phpFilePath);
            foreach($fileClasses as $class) {
                $classes->addClass($class->getFullyQualifiedName(), $phpFilePath);
            }
        }
        return $classes;
    }

    /**
     * @throws Exception
     */
    private function getPhpFiles(): array
    {
        is_dir($this->sourceDirectory) or throw new Exception("ClassScanner: path [$this->sourceDirectory] it's not directory");

        $phpFiles = $this->searchPhpFiles($this->sourceDirectory);
        foreach($phpFiles as $class => $file) {
            $phpFiles[$class] = $file;
        }
        return $phpFiles;
    }

    private function searchPhpFiles($directory): array
    {
        $phpFiles = [];
        $resources = array_diff(scandir($directory), array('.', '..'));
        foreach ($resources as $resource) {
            $path = $directory . '/' . $resource;
            if (is_dir($path)) {
                $phpFiles = array_merge($phpFiles, $this->searchPhpFiles($path));
            } else if (str_ends_with($path, ".php")) {
                $phpFiles[] = $path;
            }
        }
        return $phpFiles;
    }
}