<?php

namespace src\Infrastructure\Middleware;

use closure;
use Stormmore\Framework\App\IMiddleware;
use Stormmore\Framework\Configuration\Configuration;
use Stormmore\Framework\Mail\Mailer;
use Stormmore\Framework\Mail\Senders\IMailSender;
use Stormmore\Framework\Mail\Senders\SmtpSender;

readonly class MailerMiddleware implements IMiddleware
{
    public function __construct(private Mailer $mailer)
    {
    }

    public function run(closure $next, array $options = []): void
    {
        $this->mailer->addMailServer("local-smtp", new SmtpSender());
        $this->mailer->useMailSender("local-smtp");
        $this->mailer->addMailServer('gmail', new SmtpSender(
            "smtp.gmail.com",
            465,
            "tls",
            true,
            "christxpdev@gmail.com",
            "hkha rkze keoi agma"
        ));
        $this->mailer->useMailSender('gmail');
        $next();
    }
}



