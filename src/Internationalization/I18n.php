<?php

namespace Stormmore\Framework\Internationalization;

use Exception;
use Stormmore\Framework\UnknownPathAliasException;

class I18n
{
    public array $translations = [];

    /**
     * @throws UnknownPathAliasException
     */
    public function loadLangFile($filePath): void
    {
        $path = resolve_path_alias($filePath);
        file_exists($path) or throw new Exception("I18n: Language file [$path] doesn't exist");
        $this->translations = json_decode(file_get_contents($path), true);
    }

    public function translate($phrase): string
    {
        if (array_key_exists($phrase, $this->translations)) {
            return $this->translations[$phrase];
        }

        return $phrase;
    }
}