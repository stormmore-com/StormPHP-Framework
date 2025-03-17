<?php

namespace Stormmore\Framework\Validation\Validators;

use Stormmore\Framework\Validation\IValidator;
use Stormmore\Framework\Validation\ValidatorResult;

readonly class RegexpValidator implements IValidator
{
    public function __construct(private string $regexp, private null|string $message = null)
    {
    }

    function validate(mixed $value, string $name, array $data, mixed $args): ValidatorResult
    {
        if ($value and !preg_match($this->regexp, $value)) {
            $message = $this->message ?? _("validation.invalid_value");
            return new ValidatorResult(false, $message);
        }
        return new ValidatorResult();
    }
}