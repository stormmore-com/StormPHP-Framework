<?php

namespace Stormmore\Framework\Validation;

class ValidatorResult
{
    public function __construct(
        public bool   $valid = true,
        public string $message = "")
    {
    }
}