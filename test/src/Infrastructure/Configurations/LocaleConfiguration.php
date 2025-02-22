<?php

namespace Infrastructure\Configurations;

use Infrastructure\Settings\Settings;
use Stormmore\Framework\App\IConfiguration;
use Stormmore\Framework\Configuration\JsonConfigurationLoader;
use Stormmore\Framework\Internationalization\Culture;
use Stormmore\Framework\Internationalization\I18n;
use Stormmore\Framework\Internationalization\Locale;
use Stormmore\Framework\Request\Request;

readonly class LocaleConfiguration implements IConfiguration
{
    public function __construct(private Request $request,
                                private Settings $settings,
                                private I18n $i18n,
                                private JsonConfigurationLoader $jsonConfigurationLoader)
    {
    }

    public function configure(): void
    {
        $locale = $this->getAcceptedLocale();
        $culture = $this->getCulture($locale);

        $this->i18n->setLocale($locale);
        $this->i18n->setCulture($culture);

        $this->loadTranslations($locale);
    }

    private function getAcceptedLocale(): Locale
    {
        $defaultLocale = $this->settings->i18n->defaultLocale;
        $supportedLocales = $this->settings->i18n->locales;
        return $this->request->getFirstAcceptedLocale($supportedLocales) ?? $defaultLocale;
    }

    private function loadTranslations(Locale $locale): void
    {
        $tagFilename = "@/i18n/{$locale->tag}.json";
        $languageFilename = "@/i18n/{$locale->languageCode}.json";
        if (file_exist_alias($tagFilename)) {
            $this->i18n->loadJsonTranslations($tagFilename);
        }
        else if (file_exist_alias($languageFilename)) {
            $this->i18n->loadJsonTranslations($languageFilename);
        }
    }

    private function getCulture(Locale $locale): Culture
    {
        $culture = new Culture();
        $tagFilename = "@/i18n/culture/{$locale->tag}.json";
        $languageFilename = "@/i18n/culture/{$locale->languageCode}.json";

        if (file_exist_alias($tagFilename)) {
            $this->jsonConfigurationLoader->load($culture, $tagFilename);
        }
        else if (file_exist_alias($languageFilename)) {
            $this->jsonConfigurationLoader->load($culture, $languageFilename);
        }
        return $culture;
    }
}