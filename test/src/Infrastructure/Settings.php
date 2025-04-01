<?php

namespace Infrastructure;

use Stormmore\Framework\Configuration\Configuration;
use Stormmore\Framework\Internationalization\Locale;

class Settings
{
    public bool $isMultiLanguage;
    public Locale $defaultLocale;
    /**
     * @var Locale[]
     */
    public array $locales = [];

    public function __construct(Configuration $configuration)
    {
        $this->isMultiLanguage = $configuration->getBool("i18n.isMultiLanguage");
        $this->defaultLocale = new Locale($configuration->get('i18n.defaultLocale'));
        $this->locales = array_map(fn($item) => new Locale($item), $configuration->getArray('i18n.locales'));
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