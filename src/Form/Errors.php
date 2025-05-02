<?php

namespace Stormmore\Framework\Form;

use Stormmore\Framework\Validation\ValidationResult;

class Errors
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
}