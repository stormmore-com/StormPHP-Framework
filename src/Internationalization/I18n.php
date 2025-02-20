<?php

namespace Stormmore\Framework\Internationalization;

use DateTimeZone;
use Exception;
use Stormmore\Framework\UnknownPathAliasException;

class I18n
{
    public Culture $culture;
    public array $translations = [];

    public function __construct()
    {
        $this->culture = new Culture();
        $this->culture->timeZone = new DateTimeZone(date_default_timezone_get());
    }


    /**
     * @throws UnknownPathAliasException
     */
    public function loadLangFile($filePath): void
    {
        $path = resolve_path_alias($filePath);
        file_exists($path) or throw new Exception("I18n: Language file [$path] doesn't exist");
        $this->translations = json_decode(file_get_contents($path), true);
    }

    /**
     * @throws UnknownPathAliasException
     */
    public function loadLocalFile($filePath): void
    {
        $path = resolve_path_alias($filePath);
        file_exists($path) or throw new Exception("I18n: Locale file [$path] doesn't exist");
        $locale = json_decode(file_get_contents($path), true);

        foreach (['dateFormat', 'dateTimeFormat', 'currency', 'timeZone', 'locale'] as $key) {
            if (array_key_exists($key, $locale)) {
                $this->culture->$key = $locale[$key];
            }
        }
    }

    public function translate($phrase): string
    {
        if (array_key_exists($phrase, $this->translations)) {
            return $this->translations[$phrase];
        }

        return $phrase;
    }
}