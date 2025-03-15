<?php

namespace Stormmore\Framework\Validation\Validators;

use Stormmore\Framework\Validation\IValidator;
use Stormmore\Framework\Validation\ValidatorResult;

class RegexpValidator implements IValidator
{
    public function __construct(private readonly string $regexp)
    {
    }

    function validate(mixed $value, string $name, array $data, mixed $args): ValidatorResult
    {
        if (!preg_match($this->regexp, $value)) {
            return new ValidatorResult(false, _("validation.invalid_value"));
        }
        return new ValidatorResult();
    }
}