<?php

namespace Stormmore\Framework\Validation\Validator;

use Stormmore\Framework\Validation\IValidator;
use Stormmore\Framework\Validation\ValidatorResult;

class OptionValidator implements IValidator
{
    function validate(mixed $value, string $name, array $data, mixed $args): ValidatorResult
    {
        if (!in_array($value, $args)) {
            return new ValidatorResult(false, _("Invalid [$value] option"));
        }
        return new ValidatorResult();
    }
}