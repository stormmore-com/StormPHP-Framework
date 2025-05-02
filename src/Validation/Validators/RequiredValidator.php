<?php

namespace Stormmore\Framework\Validation\Validators;

use Stormmore\Framework\Validation\IValidator;
use Stormmore\Framework\Validation\ValidatorResult;

class RequiredValidator implements IValidator
{
    public function __construct(private readonly null|string $message = null)
    {
    }

    function validate(mixed $value, string $name, array $data, mixed $args): ValidatorResult
    {
        if ($value === null or $value === '') {
            $message = $this->message ?? t('validation.required');
            $message = array_key_value($args, 'message', $message);
            return new ValidatorResult(false, $message);
        }
        return new ValidatorResult();
    }
}