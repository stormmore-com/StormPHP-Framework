<?php

namespace Stormmore\Framework\Http;

use Stormmore\Framework\Exceptions\FileNotFoundException;
use Stormmore\Framework\Http\Fields\FileField;

class FormData
{
    /** @var Field[]  */
    private array $fields = [];

    private array $files = [];

    public function add(string $field, mixed $value): FormData
    {
        $this->fields[] = new Field($field, $value);
        return $this;
    }

    public function addFile(string $field, string $path): FormData
    {
        $path = realpath($path);
        if (!file_exists($path)) {
            throw new FileNotFoundException($path);
        }
        $this->files[] = new Field($field, $path);
        return $this;
    }

    /**
     * @return Field[]
     */
    public function getFiles(): array
    {
        return $this->files;
    }

    /**
     * @return Field[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    public function getNestedFieldsArray(): array
    {
        return $this->toNestedArray($this->fields);
    }

    public function getNestedFilesArray(): array
    {
        return $this->toNestedArray($this->files);
    }

    /**
     * @param Field[] $array
     * @return array
     */
    private function toNestedArray(array $fields): array
    {
        $parameters = [];
        foreach ($fields as $field) {
            if ($field->isArrayType()) {
                $value = &$parameters;
                $arrayPath = $field->getArrayPath();
                foreach ($arrayPath as $idx => $path) {
                    $key = false;
                    if ($path != '[]') {
                        $key = str_replace(['[', ']', "'", '"'], '', $path);
                    }
                    if ($idx < count($arrayPath) - 1) {
                        if ($key and !array_key_exists($key, $value)) {
                            $array = array();
                            $value[$key] = $array;
                        }
                        if ($key) {
                            $value = &$value[$key];
                        }
                        if (!$key) {
                            $array = array();
                            $value[] = $array;
                            $value = &$value[count($value) - 1];
                        }
                    }
                    if ($idx == count($arrayPath) - 1) {
                        if ($key) {
                            $value[$key] = $field->getValue();
                        }
                        if (!$key) {
                            $value[] = $field->getValue();
                        }
                    }
                }
            }
            else {
                $parameters[$field->getName()] = $field->getValue();
            }
        }

        return $parameters;
    }
}