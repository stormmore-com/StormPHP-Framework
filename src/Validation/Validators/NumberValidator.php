<?php

namespace Stormmore\Framework\Validation\Validators;

use Stormmore\Framework\Validation\IValidator;
use Stormmore\Framework\Validation\ValidatorResult;

readonly class NumberValidator implements IValidator
{
    public function __construct(private null|string $message = null)
    {
    }

    function validate(mixed $value, string $name, array $data, mixed $args): ValidatorResult
    {
        $message = $this->message ?? _("validation.numeric");
        if ($value and !is_numeric($value)) {
            return new ValidatorResult(false, $message);
        }
        return new ValidatorResult();
    }
}