<?php

namespace Stormmore\Framework\Validation\Validators;

use Stormmore\Framework\Validation\IValidator;
use Stormmore\Framework\Validation\ValidatorResult;

readonly class IntValidator implements IValidator
{
    public function __construct(private null|string $message = null)
    {
    }
    function validate(mixed $value, string $name, array $data, mixed $arg): ValidatorResult
    {
        $message = $this->message ?? t("validation.integer");
        if ($value and !preg_match('/^[0-9]+$/', $value)) {
            return new ValidatorResult(false, $message);
        }
        return new ValidatorResult();
    }
}