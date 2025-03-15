<?php

namespace Stormmore\Framework\Validation\Validators;

use Stormmore\Framework\Validation\IValidator;
use Stormmore\Framework\Validation\ValidatorResult;

class FloatValidator implements IValidator
{

    function validate(mixed $value, string $name, array $data, mixed $args): ValidatorResult
    {
        if ($value and !preg_match("/^[0-9-]*[\.]{1}[0-9-]+$/", $value)) {
            return new ValidatorResult(false, _("validation.float"));
        }
        return new ValidatorResult();
    }
}