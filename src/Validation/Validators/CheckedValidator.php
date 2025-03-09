<?php

namespace Stormmore\Framework\Validation\Validators;

use Stormmore\Framework\Validation\IValidator;
use Stormmore\Framework\Validation\ValidatorResult;

class CheckedValidator implements IValidator
{
    function validate(mixed $value, string $name, array $data, mixed $args): ValidatorResult
    {
        if ($value !== true) {
            return new ValidatorResult(false, _("Field has to be checked"));
        }
        return new ValidatorResult();
    }
}