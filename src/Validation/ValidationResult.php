<?php

namespace Stormmore\Framework\Validation;

class ValidationResult
{
    public bool $isValid = true;
    public array $errors = [];

    function addError(string $field, $value): void
    {
        $this->isValid = false;
        $this->errors[$field] = $value;
    }

    function isValid(): bool
    {
        return $this->isValid;
    }

    function __get($name)
    {
        $field = new FieldValidationResult();
        if (array_key_exists($name, $this->errors)) {
            $field->invalid = true;
            $field->valid = false;
            $field->message = $this->errors[$name];
        }

        return $field;
    }
}