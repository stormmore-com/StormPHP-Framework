<?php

namespace Configuration;

use Configuration\Commands\ExampleCommand;
use Configuration\Commands\ServiceCommand;
use Configuration\Events\ServiceEvent;
use Exception;
use Infrastructure\Settings\Settings;
use Stormmore\Framework\AppConfiguration;
use Stormmore\Framework\Cqs\Gate;
use Stormmore\Framework\Events\EventDispatcher;
use Stormmore\Framework\Mvc\Attributes\Controller;
use Stormmore\Framework\Mvc\Attributes\Route;
use Stormmore\Framework\Mvc\IO\Cookie\Cookie;
use Stormmore\Framework\Mvc\IO\Redirect;
use Stormmore\Framework\Mvc\IO\Request\Request;
use Stormmore\Framework\Mvc\IO\Response;
use Stormmore\Framework\Mvc\View\View;

#[Controller]
readonly class ServiceController
{
    public function __construct(private AppConfiguration $configuration,
                                private Settings $settings,
                                private Request $request,
                                private Response $response,
                                private Gate $gate,
                                private EventDispatcher $eventDispatcher)
    {
    }

    #[Route("/cqs-test")]
    public function run(): View
    {
        $this->gate->handle(new ExampleCommand());
        $this->gate->handle(new ServiceCommand());
        return view("@templates/service/cqs",[
            'history' => $this->gate->getHistory()
        ]);
    }

    #[Route("/events-test")]
    public function events(): View
    {
        $this->eventDispatcher->handle(new ServiceEvent());
        return view("@templates/service/events",[
            'history' => $this->eventDispatcher->getHistory()
        ]);
    }

    #[Route("/configuration")]
    public function index(): View
    {
        $locales = [];
        foreach ($this->settings->i18n->locales as $locale) {
            $locales[$locale->tag] = $locale->tag;
        }
        return view("@templates/service/index", [
            'configuration' => $this->configuration,
            'locales' => $locales
        ]);
    }

    #[Route("/locale/change")]
    public function changeLocale(): Redirect
    {
        $tag = $this->request->getDefault('tag', '');
        if ($this->settings->i18n->localeExists($tag)) {
            $this->response->cookies->set(new Cookie('locale', $tag));
        }
        return back();
    }

    #[Route("/url-made-only-to-throw-exception-but-it-exist")]
    public function exceptionEndpoint()
    {
        throw new Exception("Plain exception without meaningful message. Day as always.");
    }

    #[Route("/redirect-with-success")]
    public function redirectWithSuccess(): Redirect
    {
        $this->response->messages->add("success");
        return redirect();
    }

    #[Route("/redirect-with-failure")]
    public function redirectWithFailure(): Redirect
    {
        $this->response->messages->add("failure");
        return redirect();
    }

    #[Route("/form")]
    public function form(BasicForm $form): View
    {
        $form->setModel([
            'alpha' => 'abc1',
            'alphaNum' => 'abc1!',
            'radio' => '',
            'min' => 7,
            'max' => 11,
            'num' => 'abc'
        ]);
        if ($this->request->isPost()) {
            $form->validate();
        }
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        return view('@templates/service/form', [
            'form' => $form,
            'days' => $days
        ]);
    }

    #[Route('/form-custom-messages')]
    public function formCustomMessages(CustomMessagesForm $form): View
    {
        $form->setModel([
            'alpha' => 'abc1',
            'alphaNum' => 'abc1!',
            'regexp' => 'word',
            'email' => 'mailwitherror.com',
            'min' => 0,
            'max' => 11,
            'int' => 'int',
            'float' => 'float',
            'number' => 'number'
        ]);
        if ($this->request->isPost()) {
            $form->validate();
        }
        return view('@templates/service/form-custom-messages', [
            'form' => $form
        ]);
    }
}