<?php

namespace src\App\Service;

use Exception;
use src\App\Service\Commands\ExampleCommand;
use src\App\Service\Commands\ServiceCommand;
use src\App\Service\Events\ServiceEvent;
use src\Infrastructure\Settings;
use Stormmore\Framework\AppConfiguration;
use Stormmore\Framework\Configuration\Configuration;
use Stormmore\Framework\Cqs\Gate;
use Stormmore\Framework\Events\EventDispatcher;
use Stormmore\Framework\Form\Form;
use Stormmore\Framework\Internationalization\I18n;
use Stormmore\Framework\Mail\Mailer;
use Stormmore\Framework\Mvc\Attributes\Controller;
use Stormmore\Framework\Mvc\Attributes\Get;
use Stormmore\Framework\Mvc\Attributes\Route;
use Stormmore\Framework\Mvc\IO\Cookie\SetCookie;
use Stormmore\Framework\Mvc\IO\Redirect;
use Stormmore\Framework\Mvc\IO\Request;
use Stormmore\Framework\Mvc\IO\Response;
use Stormmore\Framework\Mvc\View\View;
use Stormmore\Framework\Validation\Field;

#[Controller]
readonly class ServiceController
{
    public function __construct(private AppConfiguration $configuration,
                                private Settings         $settings,
                                private Mailer           $mailer,
                                private Request          $request,
                                private Response         $response,
                                private Gate             $gate,
                                private EventDispatcher  $eventDispatcher)
    {
    }

    #[Route('/send-mail')]
    public function sendEmail(): View|Redirect
    {
        $form = (new Form($this->request))
            ->setModel(['email' => 'czerski.michal@gmail.com', 'subject' => 'Testing STMP', 'content' => 'Hello...'])
            ->add(Field::for('email')->email()->required())
            ->add(Field::for('subject')->required())
            ->add(Field::for('content')->required());

        if ($form->isSubmittedSuccessfully()) {
            $i18n = I18n::create("en", "@src/lang/en.ini", "@src/lang/culture/en-US.ini");
            $builder = $this->mailer
                ->create()
                ->withSender('admin@example.com', "Admistrator")
                ->withRecipient($form->email, "Dear friend")
                ->withCc("cc@example.com", "CC reader")
                ->withBcc("bcc@example.com", "BCC reader")
                ->withReplyTo("reply.to@exaples.com", "Replies")
                ->withSubject($form->subject)
                ->withContentTemplate('@templates/mails/contact', ['content' => $form->content], $i18n);
            if ($this->request->files->isUploaded('attachment1')) {
                $file = $this->request->files->get('attachment1');
                $builder->withAttachment($file->path, $file->name);
            }
            if ($this->request->files->isUploaded('attachment2')) {
                $file = $this->request->files->get('attachment2');
                $builder->withAttachment($file->path, $file->name);
            }
            $builder->send();

            return redirect("/send-mail", success: "Email was sent");
        }

        return view('@templates/mails/form', [
            'form' => $form
        ]);
    }

    #[Route("/cqs-test")]
    public function run(): View
    {
        $this->gate->handle(new ExampleCommand());
        $this->gate->handle(new ServiceCommand());
        return view("@templates/service/cqs", [
            'history' => $this->gate->getHistory()
        ]);
    }

    #[Route("/events-test")]
    public function events(): View
    {
        $this->eventDispatcher->handle(new ServiceEvent());
        return view("@templates/service/events", [
            'history' => $this->eventDispatcher->getHistory()
        ]);
    }

    #[Route("/configuration")]
    public function index(): View
    {
        $locales = [];
        foreach ($this->settings->locales as $locale) {
            $locales[$locale->tag] = $locale->tag;
        }
        return view("@templates/service/index", [
            'configuration' => $this->configuration,
            'settings' => $this->settings,
            'locales' => $locales,
        ]);
    }

    #[Route("/change-url")]
    public function changeUrl(): Redirect
    {
        $url = $this->request->post->get('url');
        Configuration::update('@/settings.ini', ['url' => $url]);
        return back();
    }

    #[Route("/locale/change")]
    public function changeLocale(): Redirect
    {
        $tag = $this->request->getDefault('tag', '');
        if ($this->settings->localeExists($tag)) {
            $this->response->setCookie(new SetCookie('locale', $tag));
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
        return redirect(success: true);
    }

    #[Route("/redirect-with-failure")]
    public function redirectWithFailure(): Redirect
    {
        return redirect(failure: true);
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