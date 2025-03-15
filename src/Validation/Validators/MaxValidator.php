<?php

namespace Stormmore\Framework\Validation\Validators;

use Stormmore\Framework\Validation\IValidator;
use Stormmore\Framework\Validation\ValidatorResult;

readonly class MaxValidator implements IValidator
{
    public function __construct(private int $max)
    {
    }

    function validate(mixed $value, string $name, array $data, mixed $args): ValidatorResult
    {
        if (is_numeric($value)) {
            if ($value > $this->max) {
                return new ValidatorResult(false, _("validation.max_number"));
            }
        }
        else if (is_string($value)) {
            if (mb_strlen($value) > $this->max) {
                return new ValidatorResult(false, _("validation.max_string"));
            }
        }
        return new ValidatorResult();
    }
}