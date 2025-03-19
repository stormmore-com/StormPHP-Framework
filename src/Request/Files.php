<?php

namespace Stormmore\Framework\Request;

class Files
{
    /**
     * @type UploadedFile[]
     */
    private array $files;

    public function __construct()
    {
        $this->files = $this->parseFiles();
    }

    /**
     * @param string $name
     * @return UploadedFile|null
     */
    public function get(string $name): UploadedFile|null
    {
        foreach ($this->files as $file) {
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
    public function getMany(string $name): array
    {
        $files = array();
        foreach ($this->files as $file) {
            if ($file->fieldName == $name) {
                $files[] = $file;
            }
        }

        return $files;
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

    private function parseFiles(): array
    {
        $files = array();
        foreach ($_FILES as $formFieldName => $formFieldFiles) {
            if (is_array($formFieldFiles['name'])) {
                $size = count($formFieldFiles['name']);
                $files[$formFieldName] = array();
                for ($i = 0; $i < $size; $i++) {
                    $files[$formFieldName][$i] = new UploadedFile(
                        $formFieldName,
                        $formFieldFiles['name'][$i],
                        $formFieldFiles['tmp_name'][$i],
                        $formFieldFiles['type'][$i],
                        $formFieldFiles['error'][$i],
                        $formFieldFiles['size'][$i]);
                }
            } else {
                $files[$formFieldName] = new UploadedFile(
                    $formFieldName,
                    $formFieldFiles['name'],
                    $formFieldFiles['tmp_name'],
                    $formFieldFiles['type'],
                    $formFieldFiles['error'],
                    $formFieldFiles['size']);
            }
        }

        return $files;
    }
}