<?php

namespace Stormmore\Framework\Form;

use ArrayAccess;
use Stormmore\Framework\Validation\ValidationResult;

class Errors implements ArrayAccess
{
    private null|ValidationResult $validationResult;

    public function __construct()
    {
        $this->validationResult = null;
    }

    public function setValidationResult(ValidationResult $result): void
    {
        $this->validationResult = $result;
    }

    public function __get(string $name): mixed
    {
        return $this->validationResult?->__get($name)?->message;
    }

    public function offsetExists(mixed $offset): bool
    {
        return $this->validationResult?->__get($offset) != null;
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->validationResult?->__get($offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void { }

    public function offsetUnset(mixed $offset): void { }
}