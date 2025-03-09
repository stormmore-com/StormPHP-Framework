<?php

namespace Stormmore\Framework\Validation\Validators;

use Stormmore\Framework\Validation\IValidator;
use Stormmore\Framework\Validation\ValidatorResult;

class MaxlengthValidator implements IValidator
{
    function validate(mixed $value, string $name, array $data, mixed $max): ValidatorResult
    {
        if (is_int($max) > 0 && mb_strlen($value) > $max) {
            return new ValidatorResult(false, _("Length shouldn't be greater then %s", $max));
        }
        return new ValidatorResult();
    }
}