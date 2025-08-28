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

    /**
     * @param string $name
     * @return UploadedFile|null
     */
    public function get(string $name): null|UploadedFile|array
    {
        if (array_key_exists($name, $this->uploadedFiles)) {
            return $this->uploadedFiles[$name];
        }
        return null;
    }

    /**
     * @param string $name
     * @return bool
     * Check whether request has uploaded valid file
     */
    public function has(string $name): bool
    {
        return $this->get($name)?->isUploaded() ?? false;
    }

    public function getAll(): array
    {
        return $this->uploadedFiles;
    }

    public function delete(): void
    {
        foreach($this->uploadedFiles as $file) {
            $file->delete();
        }
    }
}