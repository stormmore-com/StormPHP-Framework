<?php

namespace Stormmore\Framework\Validation\Validator;

use Stormmore\Framework\Validation\IValidator;
use Stormmore\Framework\Validation\ValidatorResult;

class IntValidator implements IValidator
{
    function validate(mixed $value, string $name, array $data, mixed $arg): ValidatorResult
    {
        if (!is_int($value)) {
            return new ValidatorResult(false, _("Field is not integer"));
        }
        return new ValidatorResult();
    }
}