<?php

namespace Stormmore\Framework\Internationalization;

use Exception;
use Stormmore\Framework\UnknownPathAliasException;

class I18n
{
    public Locale $locale;

    public Culture $culture;
    public array $translations = [];


    public function __construct()
    {
        $this->locale = new Locale();
        $this->culture = new Culture();
    }

    public function setLocale(Locale $locale): void
    {
        $this->locale = $locale;
    }

    public function setCulture(Culture $culture): void
    {
        $this->culture = $culture;
    }

    public function getLocale(): Locale
    {
        return $this->locale;
    }

    public function getCulture(): Culture
    {
        return $this->culture;
    }

    /**
     * @throws UnknownPathAliasException
     */
    public function loadJsonTranslations($filePath): void
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