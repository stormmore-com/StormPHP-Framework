<?php

namespace Configuration;

use Infrastructure\Settings\Settings;
use Stormmore\Framework\AppConfiguration;
use Stormmore\Framework\Mvc\Controller;
use Stormmore\Framework\Mvc\Route;
use Stormmore\Framework\Mvc\View;
use Stormmore\Framework\Request\Cookie;
use Stormmore\Framework\Request\Redirect;
use Stormmore\Framework\Request\Request;
use Stormmore\Framework\Request\Response;

#[Controller]
readonly class ConfigurationController
{
    public function __construct(private AppConfiguration $configuration,
                                private Settings $settings,
                                private Request $request,
                                private Response $response)
    {
    }

    #[Route("/configuration")]
    public function index(): View
    {
        $locales = [];
        foreach ($this->settings->i18n->locales as $locale) {
            $locales[$locale->tag] = $locale->tag;
        }
        return view("@/src/templates/configuration/index", [
            'configuration' => $this->configuration,
            'locales' => $locales
        ]);
    }

    #[Route("/locale/change")]
    public function changeLocale(): Redirect
    {
        $tag = $this->request->getParameter('tag', '');
        if ($this->settings->i18n->localeExists($tag)) {
            $this->response->cookies->set(new Cookie('locale', $tag));
        }
        return back();
    }
}