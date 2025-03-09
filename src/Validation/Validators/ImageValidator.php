<?php

namespace Stormmore\Framework\Validation\Validators;

use Stormmore\Framework\Validation\IValidator;
use Stormmore\Framework\Validation\ValidatorResult;

readonly class ImageValidator implements IValidator
{
    public function __construct(
        private bool $required = false,
        private array $allowed = array()
    )
    {
    }

    public static function create(bool $required = false, array $allowed = array()): ImageValidator
    {
        return new ImageValidator(required: $required, allowed: $allowed);
    }

    function validate(mixed $file, string $name, array $data, mixed $args): ValidatorResult
    {
        if ($file === null and $this->required) {
            return new ValidatorResult(false, _("validation.storm.field_is_required"));
        }
        if ($file instanceof UploadedFile) {
            if (!$file->isUploaded() and ($file->error == 1 or $this->error == 2)) {
                return new ValidatorResult(false, _("validation_storm.image.image_exceed_max_size"));
            }
            else if (!$file->isUploaded()) {
                return new ValidatorResult(false, _("validation_storm.image.file_not_upload"));
            }
            $type = exif_imagetype($file->path);
            if ($type === false || (!empty($this->allowed) and !in_array($type, $this->allowed))) {
                return new ValidatorResult(false, _("validation.storm.image_not_supported"));
            }
        }
        return new ValidatorResult();
    }
}