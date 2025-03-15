<?php

namespace Stormmore\Framework\Validation;

class ValidatorResult
{
    public function __construct(public bool $isValid = true, public string $message = "")
    {
    }
}