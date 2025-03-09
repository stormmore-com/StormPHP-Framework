<?php

namespace Stormmore\Framework\Validation\Validators;

use Stormmore\Framework\Validation\IValidator;
use Stormmore\Framework\Validation\ValidatorResult;

class RequiredValidator implements IValidator
{
    function validate(mixed $value, string $name, array $data, mixed $args): ValidatorResult
    {
        if (empty($value)) {
            $message = array_key_value($args, 'message', _('Field is required'));
            return new ValidatorResult(false, $message);
        }
        return new ValidatorResult();
    }
}