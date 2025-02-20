<?php

namespace Stormmore\Framework\Validation;

interface IValidator
{
    function validate(mixed $value, string $name, array $data, mixed $args): ValidatorResult;
}