<?php

namespace Stormmore\Framework\Validation\Validators;

use Stormmore\Framework\Mvc\IO\Request\UploadedFile;
use Stormmore\Framework\Validation\IValidator;
use Stormmore\Framework\Validation\ValidatorResult;

readonly class FileValidator implements IValidator
{

    public function __construct(private array $extensions = array(), private int $size = 0, private null|string $message = null)
    {
    }

    function validate(mixed $value, string $name, array $data, mixed $args): ValidatorResult
    {
        if ($value instanceof UploadedFile) {
            if (!empty($this->extensions)) {
                $extensions = pathinfo($value->name)['extension'];
                if (!in_array($extensions, $this->extensions)) {
                    $message = $this->message ?? _('validation.file_extension');
                    return new ValidatorResult(false, $message);
                }
            }
            if ($this->size > 0 and $value->exceedSize($this->size)) {
                $message = $this->message ?? _('validation.file_size');
                return new ValidatorResult(false, $message);
            }
        }
        return new ValidatorResult();
    }
}