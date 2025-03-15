<?php

namespace Stormmore\Framework\Validation\Validators;

use Stormmore\Framework\Request\UploadedFile;
use Stormmore\Framework\Validation\IValidator;
use Stormmore\Framework\Validation\ValidatorResult;

readonly class ImageValidator implements IValidator
{
    public function __construct(private array $allowed = array())
    {
    }

    function validate(mixed $file, string $name, array $data, mixed $args): ValidatorResult
    {
        if ($file instanceof UploadedFile) {
            if (!$file->isUploaded() and ($file->error == 1 or $this->error == 2)) {
                return new ValidatorResult(false, _("validation.image_max_size"));
            }
            else if (!$file->isUploaded()) {
                return new ValidatorResult(false, _("validation.image_not_uploaded"));
            }
            $type = exif_imagetype($file->path);
            if ($type === false || (!empty($this->allowed) and !in_array($type, $this->allowed))) {
                return new ValidatorResult(false, _("validation.image_format"));
            }
        }
        return new ValidatorResult();
    }
}