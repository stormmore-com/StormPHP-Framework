<?php

namespace Stormmore\Framework\Validation\Validator;

use Stormmore\Framework\Validation\IValidator;
use Stormmore\Framework\Validation\ValidatorResult;

class AlphaNumValidator implements IValidator
{
    function validate(mixed $value, string $name, array $data, mixed $args): ValidatorResult
    {
        if (!ctype_alnum($value)) {
            return new ValidatorResult(false, _("Allowed only alpha-numeric characters"));
        }
        return new ValidatorResult();
    }
}