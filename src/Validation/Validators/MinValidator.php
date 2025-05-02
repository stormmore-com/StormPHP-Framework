<?php

namespace Stormmore\Framework\Validation\Validators;

use Stormmore\Framework\Validation\IValidator;
use Stormmore\Framework\Validation\ValidatorResult;

readonly class MinValidator implements IValidator
{
    public function __construct(private int $min, private null|string $message = null)
    {
    }

    function validate(mixed $value, string $name, array $data, mixed $args): ValidatorResult
    {
        if ($value != '' and is_numeric($value)) {
            if ( $value < $this->min) {
                $message = $this->message ?? t("validation.min_number");
                return new ValidatorResult(false, $message);
            }
        } else if ($value != '' and is_string($value)) {
            if (mb_strlen($value) < $this->min) {
                $message = $this->message ?? t("validation.min_string");
                return new ValidatorResult(false, $message);
            }
        }
        return new ValidatorResult();
    }
}