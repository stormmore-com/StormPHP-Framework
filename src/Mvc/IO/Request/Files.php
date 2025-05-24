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
        //$this->parseFiles($files);
    }

    /**
     * @param string $name
     * @return UploadedFile|null
     */
    public function get(string $name): UploadedFile|null
    {
        foreach ($this->uploadedFiles as $file) {
            if ($file->fieldName == $name) {
                return $file;
            }
        }

        return null;
    }

    /**
     * @param string $name
     * @return UploadedFile[]
     */
    public function getAll(string $name): array
    {
        if (array_key_exists($name, $this->uploadedFiles)) {
            return $this->uploadedFiles[$name];
        }
        return [];
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

    public function toArray(): array
    {
        return $this->uploadedFiles;
    }

    public function delete(): void
    {
        foreach($this->uploadedFiles as $file) {
            $file->delete();
        }
    }

    private function parseFiles(array $files): array
    {
        foreach ($files as $formFieldName => $formFieldFiles) {
            if (is_array($formFieldFiles['name'])) {
                $size = count($formFieldFiles['name']);
                $this->uploadedFiles[$formFieldName] = array();
                for ($i = 0; $i < $size; $i++) {
                    $this->uploadedFiles[$formFieldName][$i] = new UploadedFile(
                        $formFieldName,
                        $formFieldFiles['name'][$i],
                        $formFieldFiles['tmp_name'][$i],
                        $formFieldFiles['type'][$i],
                        $formFieldFiles['error'][$i],
                        $formFieldFiles['size'][$i]);
                }
            } else {
                $this->uploadedFiles[$formFieldName] = new UploadedFile(
                    $formFieldName,
                    $formFieldFiles['name'],
                    $formFieldFiles['tmp_name'],
                    $formFieldFiles['type'],
                    $formFieldFiles['error'],
                    $formFieldFiles['size']);
            }
        }

        return $this->uploadedFiles;
    }
}