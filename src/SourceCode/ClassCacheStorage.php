<?php

namespace Stormmore\Framework\SourceCode;
use Stormmore\Framework\AppConfiguration;

class ClassCacheStorage
{
    private string $cacheDirectory;
    private string $cacheFilePath;

    function __construct(AppConfiguration $configuration, $fileName)
    {
        $this->cacheDirectory = $configuration->getCacheDirectory();
        $this->cacheFilePath = concatenate_paths($this->cacheDirectory, $fileName);
    }

    function exist(): bool
    {
        return file_exists($this->cacheFilePath);
    }

    function save(array $var): void
    {
        if (!is_dir($this->cacheDirectory)) {
            mkdir($this->cacheDirectory, 0777, true);
        }

        $serialized = serialize($var);
        file_put_contents($this->cacheFilePath, $serialized);
    }

    function load(): array
    {
        $classes = file_get_contents($this->cacheFilePath);
        return unserialize($classes);
    }
}