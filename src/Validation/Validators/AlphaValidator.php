<?php

namespace Stormmore\Framework\Validation\Validator;

use Stormmore\Framework\Validation\IValidator;
use Stormmore\Framework\Validation\ValidatorResult;

class AlphaValidator implements IValidator
{
    function validate(mixed $value, string $name, array $data, mixed $args): ValidatorResult
    {
        if (!ctype_alpha($value)) {
            return new ValidatorResult(false, _("Allowed only alphabetic characters"));
        }
        return new ValidatorResult();
    }
}