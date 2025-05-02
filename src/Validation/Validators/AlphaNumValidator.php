<?php

namespace Stormmore\Framework\Validation\Validators;

use Stormmore\Framework\Validation\IValidator;
use Stormmore\Framework\Validation\ValidatorResult;

class AlphaNumValidator implements IValidator
{

    public function __construct(readonly private null|string $message = null)
    {
    }

    function validate(mixed $value, string $name, array $data, mixed $args): ValidatorResult
    {
        if ($value and !ctype_alnum($value)) {
            $message = $this->message ?? t("validation.alpha-numeric");
            return new ValidatorResult(false, $message);
        }
        return new ValidatorResult();
    }
}