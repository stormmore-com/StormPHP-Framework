<?php

namespace Stormmore\Framework\Validation\Validators;

use DateTime;
use Exception;
use Stormmore\Framework\Validation\IValidator;
use Stormmore\Framework\Validation\ValidatorResult;

class DateTimeValidator implements IValidator
{
    public function __construct(private readonly ?string $message = null)
    {
    }

    function validate(mixed $value, string $name, array $data, mixed $args): ValidatorResult
    {
        if ($value and !$value instanceof DateTime) {
            try {
                new DateTime($value);
            }
            catch (Exception) {
                $message = $this->message ?? _("validation.invalid_date");
                return new ValidatorResult(false, $message);
            }

        }

        return new ValidatorResult();
    }
}