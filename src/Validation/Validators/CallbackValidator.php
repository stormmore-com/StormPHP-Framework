<?php

namespace Stormmore\Framework\Validation\Validators;

use closure;
use Stormmore\Framework\Validation\IValidator;
use Stormmore\Framework\Validation\ValidatorResult;

readonly class CallbackValidator implements IValidator
{
    public function __construct(private closure $closure, private string $message)
    {
    }

    function validate(mixed $value, string $name, array $data, mixed $args): ValidatorResult
    {
        if (!$this->closure->__invoke($name, $value)) {
            return new ValidatorResult(false, $this->message);
        }
        return new ValidatorResult();
    }
}