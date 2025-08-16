<?php

namespace Stormmore\Framework\Internationalization;

use closure;
use Stormmore\Framework\App\IMiddleware;
use Stormmore\Framework\Configuration\Configuration;
use Stormmore\Framework\DependencyInjection\Container;
use Stormmore\Framework\Mvc\IO\Request;

readonly class LanguageMiddleware implements IMiddleware
{
    public function __construct(private Request       $request,
                                private Configuration $configuration,
                                private I18n          $i18n,
                                private Container $container)
    {
    }

    public function run(closure $next, array $options = []): void
    {
        $locale = $this->getAcceptedLocale();

        $this->i18n->setLocale($locale);

        $this->loadTranslations($locale);
        $this->loadCulture($locale);

        $this->container->register($locale);

        $next();
    }

    private function getAcceptedLocale(): Locale
    {
        $languages = [];
        $multiLanguage = false;
        $defaultLanguage = 'en_US';
        $cookieName = "locale";

        if ($this->configuration->has('i18n.multi_language')) {
            $multiLanguage = $this->configuration->get('i18n.multi_language');
        }
        if ($this->configuration->has('i18n.default_language')) {
            $defaultLanguage = $this->configuration->get('i18n.default_language');
        }
        if ($this->configuration->has('i18n.languages')) {
            $languages = $this->configuration->getArray('i18n.languages');
        }

        if ($this->configuration->has('i18n.cookie.name')) {
            $cookieName = $this->configuration->get('i18n.cookie.name');
        }

        if (!$multiLanguage) {
            return new Locale($defaultLanguage);
        }

        if ($this->request->hasCookie($cookieName)) {
            $tag = $this->request->getCookie($cookieName)->getValue();
            if (in_array($tag, $languages)) {
                return new Locale($tag);
            }
        }

        $locales = array_map(fn($tag) => new Locale($tag), $languages);

        return $this->request->getFirstAcceptedLocale($locales) ?? new Locale($defaultLanguage);
    }

    private function loadTranslations(Locale $locale): void
    {
        $filePattern = "";
        if ($this->configuration->has('i18n.translation.file_pattern')) {
            $filePattern = $this->configuration->get('i18n.translation.file_pattern');
        }

        $tagFilename = str_replace('%file%', $locale->tag, $filePattern);
        $languageFilename = str_replace('%file%', $locale->languageCode, $filePattern);
        if (file_path_exist($tagFilename)) {
            $this->i18n->loadTranslations($tagFilename);
        }
        if (file_path_exist($languageFilename)) {
            $this->i18n->loadTranslations($languageFilename);
        }
    }

    private function loadCulture(Locale $locale): void
    {
        $filePattern = "";
        if ($this->configuration->has('i18n.culture.file_pattern')) {
            $filePattern = $this->configuration->get('i18n.culture.file_pattern');
        }

        $tagFilename = str_replace('%file%', $locale->tag, $filePattern);
        $languageFilename = str_replace('%file%', $locale->languageCode, $filePattern);
        if (file_path_exist($tagFilename)) {
            $this->i18n->loadCulture($tagFilename);
        }
        if (file_path_exist($languageFilename)) {
            $this->i18n->loadCulture($languageFilename);
        }
    }
}