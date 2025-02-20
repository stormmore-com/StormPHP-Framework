<?php

namespace Stormmore\Framework\Validation\Validator;

use Stormmore\Framework\Validation\IValidator;
use Stormmore\Framework\Validation\ValidatorResult;

class EmailValidator implements IValidator
{
    function validate(mixed $email, string $name, array $data, mixed $args): ValidatorResult
    {
        if (!self::isValidEmail($email)) {
            return new ValidatorResult(false, _("It's not a valid email address"));
        }
        return new ValidatorResult();
    }

    public static function isValidEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
}