<?php

namespace Stormmore\Framework\Validation\Validators;

use Stormmore\Framework\Validation\IValidator;
use Stormmore\Framework\Validation\ValidatorResult;

class EmailValidator implements IValidator
{
    public function __construct(private readonly null|string $message = null)
    {
    }

    function validate(mixed $value, string $name, array $data, mixed $args): ValidatorResult
    {
        if ($value and !self::isValidEmail($value)) {
            $message = $this->message ?? _("validation.email");
            return new ValidatorResult(false, $message);
        }
        return new ValidatorResult();
    }

    public static function isValidEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
}