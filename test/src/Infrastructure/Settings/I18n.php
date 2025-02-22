<?php

namespace Infrastructure\Settings;

use Stormmore\Framework\Internationalization\Locale;

class I18n
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

    public function isSupportedLocale(string $locale): bool
    {
        foreach($this->locales as $supported) {
            if ($supported->tag === $locale->tag) {
                return true;
            }
        }
        return false;
    }

    public function localeExists(string $locale): bool
    {
        foreach($this->locales as $supported) {
            if ($supported->tag === $locale) {
                return true;
            }
        }
        return false;
    }
}