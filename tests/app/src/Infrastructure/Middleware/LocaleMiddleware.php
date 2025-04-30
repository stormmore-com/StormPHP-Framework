<?php

namespace src\Infrastructure\Middleware;

use closure;
use src\Infrastructure\Settings;
use Stormmore\Framework\App\IMiddleware;
use Stormmore\Framework\Configuration\IConfiguration;
use Stormmore\Framework\Internationalization\I18n;
use Stormmore\Framework\Internationalization\Locale;
use Stormmore\Framework\Mvc\IO\Request\Request;

readonly class LocaleMiddleware implements IMiddleware
{
    public function __construct(private Request $request,
                                private Settings $settings,
                                private I18n $i18n)
    {
    }

    private function getAcceptedLocale(): Locale
    {
        if ($this->request->hasCookie('locale')) {
            $tag = $this->request->getCookie('locale');
            if ($this->settings->localeExists($tag)) {
                return new Locale($tag);
            }
        }
        $defaultLocale = $this->settings->defaultLocale;
        $supportedLocales = $this->settings->locales;
        return $this->request->getFirstAcceptedLocale($supportedLocales) ?? $defaultLocale;
    }

    private function loadTranslations(Locale $locale): void
    {
        $tagFilename = "@/i18n/$locale->tag.conf";
        $languageFilename = "@/i18n/$locale->languageCode.conf";
        if (file_path_exist($tagFilename)) {
            $this->i18n->loadTranslations($tagFilename);
        }
        if (file_path_exist($languageFilename)) {
            $this->i18n->loadTranslations($languageFilename);
        }
    }

    private function loadCulture(Locale $locale): void
    {
        $tagFilename = "@/i18n/culture/{$locale->tag}.conf";
        $languageFilename = "@/i18n/culture/{$locale->languageCode}.conf";
        if (file_path_exist($tagFilename)) {
            $this->i18n->loadCulture($tagFilename);
        }
        if (file_path_exist($languageFilename)) {
            $this->i18n->loadCulture($languageFilename);
        }
    }

    public function run(closure $next): void
    {
        $locale = $this->getAcceptedLocale();

        $this->i18n->setLocale($locale);

        $this->loadTranslations($locale);
        $this->loadCulture($locale);

        $next();
    }
}