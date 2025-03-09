<?php

namespace Stormmore\Framework\Validation\Validators;

use Stormmore\Framework\Validation\IValidator;
use Stormmore\Framework\Validation\ValidatorResult;

class NumberValidator implements IValidator
{
    function validate(mixed $value, string $name, array $data, mixed $args): ValidatorResult
    {
        if (!is_numeric($value)) {
            return new ValidatorResult(false, _("It's not a number"));
        }
        return new ValidatorResult();
    }
}