<?php

namespace src\Infrastructure\Middleware;

use closure;
use Stormmore\Framework\App\IMiddleware;
use Stormmore\Framework\Configuration\Configuration;
use Stormmore\Framework\Mail\Mailer;
use Stormmore\Framework\Mail\Senders\IMailSender;
use Stormmore\Framework\Mail\Senders\SmtpSender;

readonly class SettingsMiddleware implements IMiddleware
{
    public function __construct(private Configuration $configuration, private Mailer $mailer)
    {
    }

    public function run(closure $next): void
    {
        $this->mailer->addMailServer("local-smtp", new SmtpSender());
        $this->mailer->useMailSender("local-smtp");
        $this->configuration->loadFile('@/settings.conf');
        $next();
    }
}



