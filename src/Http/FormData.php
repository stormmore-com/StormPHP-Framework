<?php

namespace Stormmore\Framework\Http;

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
        $this->files[] = new Field($field, $path);
        return $this;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function getFiles(): array
    {
        return $this->files;
    }

    public function toArray(): array
    {
        $parameters = [];
        foreach ($this->fields as $field) {
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