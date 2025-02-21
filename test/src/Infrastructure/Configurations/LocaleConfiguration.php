<?php

namespace Infrastructure\Configurations;

use Infrastructure\Settings;
use Stormmore\Framework\App\IConfiguration;
use Stormmore\Framework\DependencyInjection\Container;
use Stormmore\Framework\Request\Request;

readonly class LocaleConfiguration implements IConfiguration
{
    public function __construct(private Request $request, private Settings $settings, private Container $container)
    {
    }

    public function configure(): void
    {
        if ($this->settings->isMultiLanguage) {
            $locale = $this->request->getFirstAcceptedLanguage($this->settings->locales) ?? $this->settings->defaultLocale;
            $this->container->register($locale);
        } else {
            $this->container->register($this->settings->defaultLocale);
        }
    }
}