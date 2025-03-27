<?php

namespace Stormmore\Framework\Validation\Validators;

use DateTime;
use Exception;
use Stormmore\Framework\Validation\IValidator;
use Stormmore\Framework\Validation\ValidatorResult;

class DateTimeRangeValidator implements IValidator
{
    public function __construct(private readonly ?DateTime $before = null,
                                private readonly ?DateTime $after = null,
                                private readonly ?string $message = null)
    {
    }

    function validate(mixed $value, string $name, array $data, mixed $args): ValidatorResult
    {
        if ($value) {
            if (!$value instanceof DateTime) {
                try {
                    $value = new DateTime($value);
                }
                catch (Exception) {
                    return new ValidatorResult();
                }
            }

            if ($this->after != null and $value < $this->after) {
                $message = $this->message ?? _("validation.date_min");
                return new ValidatorResult(false, $message);
            }
            if ($this->before != null and $value > $this->before) {
                $message = $this->message ?? _("validation.date_max");
                return new ValidatorResult(false, $message);
            }
        }

        return new ValidatorResult();
    }
}