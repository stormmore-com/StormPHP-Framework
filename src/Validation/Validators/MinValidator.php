<?php

namespace Stormmore\Framework\Validation\Validators;

use Stormmore\Framework\Validation\IValidator;
use Stormmore\Framework\Validation\ValidatorResult;

class MinValidator implements IValidator
{
    function validate(mixed $value, string $name, array $data, mixed $args): ValidatorResult
    {
        if (is_numeric($value)) {
            if (count($args) > 0 && $value < $args[0]) {
                return new ValidatorResult(false, _("Value should be at least %s", $args[0]));
            }
        } else if (is_string($value)) {
            if (count($args) > 0 && mb_strlen($value) < $args[0]) {
                return new ValidatorResult(false, _("Length should be at least %s", $args[0]));
            }
        }
        return new ValidatorResult();
    }
}