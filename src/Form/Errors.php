<?php

namespace Stormmore\Framework\Form;

use IteratorAggregate;
use Stormmore\Framework\Validation\ValidationResult;
use Traversable;
use ArrayIterator;

class Errors implements IteratorAggregate
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

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->validationResult?->getErrors() ?? []);
    }
}