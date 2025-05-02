<?php

namespace Stormmore\Framework\Validation\Validators;

use Stormmore\Framework\Mvc\IO\Request\UploadedFile;
use Stormmore\Framework\Validation\IValidator;
use Stormmore\Framework\Validation\ValidatorResult;

readonly class ImageValidator implements IValidator
{
    public function __construct(private array $allowed = array(), private null|string $message = null)
    {
    }

    function validate(mixed $file, string $name, array $data, mixed $args): ValidatorResult
    {
        if ($file instanceof UploadedFile) {
            if (!$file->isUploaded() and ($file->error == 1 or $this->error == 2)) {
                $message = $this->message ?? t("validation.image_max_size");
                return new ValidatorResult(false, $message);
            }
            else if (!$file->isUploaded()) {
                $message = $this->message ?? t("validation.image_not_uploaded");
                return new ValidatorResult(false, $message);
            }
            $type = exif_imagetype($file->path);
            if ($type === false || (!empty($this->allowed) and !in_array($type, $this->allowed))) {
                $message = $this->message ?? t("validation.image_format");
                return new ValidatorResult(false, t($message));
            }
        }
        return new ValidatorResult();
    }
}