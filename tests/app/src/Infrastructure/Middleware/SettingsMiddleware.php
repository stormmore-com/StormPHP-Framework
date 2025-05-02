<?php

namespace src\Infrastructure\Middleware;

use app\src\Infrastructure\OutputEmailSender;
use closure;
use Stormmore\Framework\App\IMiddleware;
use Stormmore\Framework\Configuration\Configuration;
use Stormmore\Framework\Mail\Mailer;
use Stormmore\Framework\Mail\Senders\IMailSender;

readonly class SettingsMiddleware implements IMiddleware
{
    public function __construct(private Configuration $configuration, private Mailer $mailer)
    {
    }

    public function run(closure $next): void
    {
        $this->mailer->addMailServer("output", new OutputEmailSender());
        $this->configuration->loadFile('@/settings.conf');
        $next();
    }
}



