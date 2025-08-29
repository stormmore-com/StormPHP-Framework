<?php

namespace Stormmore\Framework\Mvc\IO\Request;

class Files
{
    /**
     * @type UploadedFile[]
     */
    private array $uploadedFiles = [];

    public function __construct(array $files)
    {
        $this->uploadedFiles = $files;
    }

    public function get(string $name): null|UploadedFile|array
    {
        if (array_key_exists($name, $this->uploadedFiles)) {
            return $this->uploadedFiles[$name];
        }
        return null;
    }

    public function has(string $name): bool
    {
        return array_key_exists($name, $this->uploadedFiles);
    }

    public function getAll(): array
    {
        return $this->uploadedFiles;
    }
}