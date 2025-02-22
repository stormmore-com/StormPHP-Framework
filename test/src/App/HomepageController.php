<?php

use Infrastructure\Settings\Settings;
use Stormmore\Framework\Mvc\Controller;
use Stormmore\Framework\Mvc\Route;
use Stormmore\Framework\Mvc\View;
use Stormmore\Framework\Request\Redirect;
use Stormmore\Framework\Request\Request;
use Stormmore\Framework\Request\Response;

#[Controller]
readonly class HomepageController
{
    public function __construct(private Request $request, private Response $response, private Settings $settings)
    {
    }

    #[Route("/")]
    public function index(): View
    {
        return view("@/src/templates/homepage", ['name' => 'John Doe']);
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