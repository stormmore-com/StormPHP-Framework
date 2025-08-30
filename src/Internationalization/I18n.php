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

    public static function load(string $translationFile): I18n
    {
        $i18n = new self();
        $i18n->loadTranslations($translationFile);
        return $i18n;
    }

    public static function create(string $locale = "", string $translationFile = "", string $cultureFile = ""): I18n
    {
        $i18n = new self();
        $i18n->setLocale(new Locale($locale));
        $i18n->loadTranslations($translationFile);
        if ($cultureFile) {
            $i18n->loadCulture($cultureFile);
        }
        return $i18n;
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

    public function t(string $phrase): string
    {
        return $this->translate($phrase);
    }
}