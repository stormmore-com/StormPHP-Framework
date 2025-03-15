<?php

namespace Stormmore\Framework\Validation\Validators;

use Stormmore\Framework\Request\UploadedFile;
use Stormmore\Framework\Validation\IValidator;
use Stormmore\Framework\Validation\ValidatorResult;

readonly class FileValidator implements IValidator
{

    public function __construct(private array $extensions = array(), private int $size = 0)
    {
    }

    function validate(mixed $value, string $name, array $data, mixed $args): ValidatorResult
    {
        if ($value instanceof UploadedFile) {
            if (!empty($this->extensions)) {
                $extensions = pathinfo($value->name)['extension'];
                if (!in_array($extensions, $this->extensions)) {
                    return new ValidatorResult(false, "File extension not allowed");
                }
            }
            if ($this->size > 0 and $value->exceedSize($this->size)) {
                return new ValidatorResult(false, "File exceed max. file size");
            }
        }
        return new ValidatorResult();
    }
}