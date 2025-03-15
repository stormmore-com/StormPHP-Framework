<?php

namespace Stormmore\Framework\Validation\Validators;

use Stormmore\Framework\Validation\IValidator;
use Stormmore\Framework\Validation\ValidatorResult;

class MinValidator implements IValidator
{
    public function __construct(private int $min)
    {
    }
    function validate(mixed $value, string $name, array $data, mixed $args): ValidatorResult
    {
        if (is_numeric($value)) {
            if ( $value < $this->min) {
                return new ValidatorResult(false, _("Value should be at least %s", $this->min));
            }
        } else if (is_string($value)) {
            if (mb_strlen($value) < $this->min) {
                return new ValidatorResult(false, _("Length should be at least %s", $this->min));
            }
        }
        return new ValidatorResult();
    }
}