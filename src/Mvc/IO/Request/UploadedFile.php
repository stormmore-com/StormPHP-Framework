<?php

namespace Stormmore\Framework\Mvc\IO\Request;

class UploadedFile
{
    function __construct(
        public string $fieldName,
        public string $name,
        public string $path,
        public string $type,
        public int    $error,
        public int    $size
    )
    {
    }

    public static function createFromFile(string $file):UploadedFile
    {
        return null;
    }

    public function isImage(): bool
    {
        return $this->isUploaded() and getimagesize($this->tmp) !== false;
    }

    public function delete(): void
    {
        unlink($this->tmp);
    }

    /**
     * Check whether file was uploaded by user
     * @return bool
     */
    public function wasUploaded(): bool
    {
        return $this->error != 4;
    }

    /**
     * Check whether file was uploaded successfully
     * @return bool
     */
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
        $filename = $this->name;
        if (is_array_key_value_equal($options, 'filename', true)) {
            $filename = $options['filename'];
        }
        if (is_array_key_value_equal($options, 'gen-unique-filename', true)) {
            $length = array_key_value($options, 'gen-filename-len', 64);
            list(, $extension) = split_file_name_and_ext($this->name);
            $filename = gen_unique_file_name($length, $extension, $directory);
        }
        if (move_uploaded_file($this->tmp, $directory . "/" . $filename)) {
            $this->name = $filename;
            return true;
        }

        return false;
    }
}