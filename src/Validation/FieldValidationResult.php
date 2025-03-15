<?php

namespace Stormmore\Framework\Validation;

class FieldValidationResult
{
    public bool $valid = true;
    public bool $invalid = false;
    public string $message = "";

    public function __toString()
    {
        return $this->message;
    }
}