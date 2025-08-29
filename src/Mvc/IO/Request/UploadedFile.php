<?php

namespace Stormmore\Framework\Mvc\IO\Request;

use Exception;
use Stormmore\Framework\Std\Path;

class UploadedFile
{
    function __construct(
        public string   $name,
        public string   $path,
        public string   $type,
        public null|int $error = null,
        public null|int $size = null
    )
    {
    }

    public function isImage(): bool
    {
        return $this->isUploaded() and getimagesize($this->path) !== false;
    }

    public function delete(): void
    {
        unlink($this->path);
    }

    public function wasUploaded(): bool
    {
        return $this->error != 4;
    }

    public function isUploaded(): bool
    {
        return $this->error == 0;
    }

    /**
     * @param int $maxSize (KB)
     * @return int
     */
    public function exceedSize(int $maxSize): int
    {
        return $this->size > ($maxSize * 1024);
    }

    /**
     * @param string $directory directory to write file
     * @param array $options
     * @return bool
     */
    public function move(string $directory, array $options = []): bool
    {
        $directory = Path::resolve_alias($directory);
        $filename = $this->name;
        if (is_array_key_value_equal($options, 'filename', true)) {
            $filename = $options['filename'];
        }
        if (is_array_key_value_equal($options, 'gen-unique-filename', true)) {
            $length = array_key_value($options, 'gen-filename-len', 64);
            list(, $extension) = split_file_name_and_ext($this->name);
            $filename = Path::gen_unique_file_name($length, $extension, $directory);
        }
        $filePath = $directory . "/" . $filename;
        if (move_uploaded_file($this->path, $filePath)) {
            $this->name = $filename;
            return true;
        }
        else {
            throw new Exception("Failed to save uploaded file to $filePath");
        }

        return false;
    }
}