<?php

namespace Stormmore\Framework\Validation\Validators;

use Stormmore\Framework\Validation\IValidator;
use Stormmore\Framework\Validation\ValidatorResult;

class AlphaValidator implements IValidator
{
    public function __construct(private readonly null|string $message = null)
    {
    }

    function validate(mixed $value, string $name, array $data, mixed $args): ValidatorResult
    {
        if ($value and !ctype_alpha($value)) {
            $message = $this->message ?? _("validation.alpha");
            return new ValidatorResult(false, $message);
        }
        return new ValidatorResult();
    }
}