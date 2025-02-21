<?php

namespace Infrastructure;

use Stormmore\Framework\Internationalization\Locale;

class Settings
{
    public bool $isMultiLanguage;
    public Locale $defaultLocale;
    /**
     * @var Locale[]
     */
    public array $locales;


    public function setDefaultLocale(string $locale): void
    {
        $this->defaultLocale = new Locale($locale);
    }

    public function setLocales(array $locales): void
    {
        foreach($locales as $locale) {
            $this->locales[] = new Locale($locale);
        }
    }
}