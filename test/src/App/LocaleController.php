<?php

use Infrastructure\Settings\Settings;
use Stormmore\Framework\Mvc\Controller;
use Stormmore\Framework\Request\Redirect;
use Stormmore\Framework\Mvc\Route;
use Stormmore\Framework\Request\Request;
use Stormmore\Framework\Request\Response;

#[Controller]
readonly class LocaleController
{
    public function __construct(private Request $request, private Response $response, private Settings $settings)
    {
    }

    #[Route("/locale/change")]
    public function changeLocale(): Redirect
    {
        $tag = $this->request->getParameter('tag', '');
        if ($this->settings->i18n->localeExists($tag)) {
            $this->response->setCookie('locale', $tag);
        }
        return back();
    }
}