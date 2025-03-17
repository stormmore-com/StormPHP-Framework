<?php

namespace Stormmore\Framework\Validation\Validators;

use Stormmore\Framework\Validation\IValidator;
use Stormmore\Framework\Validation\ValidatorResult;

readonly class MaxValidator implements IValidator
{
    public function __construct(private int $max, private null|string $message = null)
    {
    }

    function validate(mixed $value, string $name, array $data, mixed $args): ValidatorResult
    {
        if (is_numeric($value)) {
            if ($value > $this->max) {
                $message = $this->message ?? _("validation.max_number");
                return new ValidatorResult(false, $message);
            }
        }
        else if (is_string($value)) {
            if (mb_strlen($value) > $this->max) {
                $message = $this->message ?? _("validation.max_string");
                return new ValidatorResult(false, $message);
            }
        }
        return new ValidatorResult();
    }
}