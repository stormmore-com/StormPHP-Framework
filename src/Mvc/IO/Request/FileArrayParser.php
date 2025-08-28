<?php

namespace Stormmore\Framework\Mvc\IO\Request;

use stdClass;
use Stormmore\Framework\Std\Collection;

class FileArrayParser
{
    public function parse(array $files): array
    {
        $result = [];
        foreach ($files as $field => $fileData) {
            $result[$field] = $this->convertFileTree($fileData);
        }

        return $result;
    }

    private function convertFileTree(array $fileTree): mixed
    {
        if (is_array($fileTree['name'])) {
            $result = [];
            foreach ($fileTree['name'] as $key => $value) {
                $result[$key] = $this->convertFileTree([
                    'name'     => $fileTree['name'][$key],
                    'type'     => $fileTree['type'][$key],
                    'tmp_name' => $fileTree['tmp_name'][$key],
                    'error'    => $fileTree['error'][$key],
                    'size'     => $fileTree['size'][$key],
                ]);
            }
            return $result;
        }

        return new UploadedFile(
            $fileTree['name'],
            $fileTree['tmp_name'],
            $fileTree['type'],
            $fileTree['error'],
            $fileTree['size']
        );
    }
}