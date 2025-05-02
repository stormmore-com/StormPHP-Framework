<?php

namespace Stormmore\Framework\Validation\Validators;

use Stormmore\Framework\Validation\IValidator;
use Stormmore\Framework\Validation\ValidatorResult;

readonly class ValuesValidator implements IValidator
{
    public function __construct(private array $values, private null|string $message = null)
    {
    }

    function validate(mixed $value, string $name, array $data, mixed $args): ValidatorResult
    {
        $message = $this->message ?? t("validation.invalid_value");
        if (is_array($value)) {
            $diff = array_diff($value, $this->values);
            if (count($diff)) {
                return new ValidatorResult(false, $message);
            }
        }
        else if (!in_array($value, $this->values)) {
            return new ValidatorResult(false, $message);
        }
        return new ValidatorResult();
    }
}