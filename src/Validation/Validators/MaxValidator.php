<?php

namespace Stormmore\Framework\Validation\Validators;

use Stormmore\Framework\Validation\IValidator;
use Stormmore\Framework\Validation\ValidatorResult;

class MaxValidator implements IValidator
{
    function validate(mixed $value, string $name, array $data, mixed $args): ValidatorResult
    {
        if (is_string($value)) {
            if (count($args) > 0 && mb_strlen($value) > $args[0]) {
                return new ValidatorResult(false, _("Length shouldn't be greater then %s", $args[0]));
            }
        }
        if (is_numeric($value)) {
            if (count($args) > 0 && $value > $args[0]) {
                return new ValidatorResult(false, _("Value shouldn't be greater then %s", $args[0]));
            }
        }
        return new ValidatorResult();
    }
}