<?php

namespace Stormmore\Framework\Validation\Validators;

use Stormmore\Framework\Validation\IValidator;
use Stormmore\Framework\Validation\ValidatorResult;

readonly class DomainUrlValidator implements IValidator
{
    public function __construct(private null|string $message = "validation.domain")
    {
    }

    function validate(mixed $value, string $name, array $data, mixed $args): ValidatorResult
    {
        $regex = '#^(http|https)://([a-zA-Z0-9][a-zA-Z0-9-]{1,61}[a-zA-Z0-9]\.)+[a-zA-Z]{2,}$#';
        if ($value and !preg_match($regex, $value)) {
            return new ValidatorResult(false, $this->message);
        }
        return new ValidatorResult();
    }
}