<?php

namespace Stormmore\Framework\Internationalization;

use Stormmore\Framework\Configuration\Configuration;
use Stormmore\Framework\UnknownPathAliasException;

class I18n
{
    public Locale $locale;

    public Culture $culture;
    public ?Configuration $translations;


    public function __construct()
    {
        $this->translations = null;
        $this->locale = new Locale();
        $this->culture = new Culture();
    }

    public function setLocale(Locale $locale): void
    {
        $this->locale = $locale;
    }

    public function loadCulture(string $filepath): void
    {
        $configuration = Configuration::createFromFile($filepath);
        $culture = new Culture();
        $culture->currency = $configuration->get('culture.currency');
        $culture->dateFormat = $configuration->get('culture.date-format');
        $culture->dateTimeFormat = $configuration->get('culture.date-time-format');
        $this->setCulture($culture);
    }

    public function setCulture(Culture $culture): void
    {
        $this->culture = $culture;
    }

    public function loadTranslations(string $filepath): void
    {
        $configuration = Configuration::createFromFile($filepath);
        $this->setTranslations($configuration);
    }

    public function setTranslations(Configuration $translations): void
    {
        $this->translations = $translations;
    }

    public function getLocale(): Locale
    {
        return $this->locale;
    }

    public function getCulture(): Culture
    {
        return $this->culture;
    }

    public function translate($phrase): string
    {
        if ($this->translations?->has($phrase)) {
            return $this->translations->get($phrase);
        }

        return $phrase;
    }
}