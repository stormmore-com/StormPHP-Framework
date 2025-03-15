<?php

namespace Stormmore\Framework\Validation\Validators;

use Stormmore\Framework\Validation\IValidator;
use Stormmore\Framework\Validation\ValidatorResult;

class ValuesValidator implements IValidator
{
    public function __construct(private readonly array $values)
    {
    }

    function validate(mixed $value, string $name, array $data, mixed $args): ValidatorResult
    {
        if (is_array($value)) {
            $diff = array_diff($value, $this->values);
            if (count($diff)) {
                return new ValidatorResult(false, _("Invalid " . implode(',', $diff) . " value"));
            }
        }
        else if (!in_array($value, $this->values)) {
            return new ValidatorResult(false, _("Invalid value `{$value}`"));
        }
        return new ValidatorResult();
    }
}