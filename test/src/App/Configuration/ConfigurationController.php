<?php

namespace Configuration;

use Infrastructure\Settings\Settings;
use Stormmore\Framework\AppConfiguration;
use Stormmore\Framework\Mvc\Controller;
use Stormmore\Framework\Mvc\Route;
use Stormmore\Framework\Mvc\View;

#[Controller]
readonly class ConfigurationController
{
    public function __construct(private AppConfiguration $configuration, private Settings $settings)
    {

    }

    #[Route("/configuration")]
    public function index(): View
    {
        $locales = [];
        foreach ($this->settings->i18n->locales as $locale) {
            $locales[$locale->tag] = $locale->tag;
        }
        return view("@/src/templates/configuration/status", [
            'configuration' => $this->configuration,
            'locales' => $locales
        ]);
    }
}