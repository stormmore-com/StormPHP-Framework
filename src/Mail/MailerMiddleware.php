<?php

namespace Stormmore\Framework\Mail;

use Exception;
use closure;
use Stormmore\Framework\App\IMiddleware;
use Stormmore\Framework\Configuration\Configuration;
use Stormmore\Framework\Mail\Senders\SmtpSender;

readonly class MailerMiddleware implements IMiddleware
{
    public function __construct(private Configuration $configuration, private Mailer $mailer)
    {
    }

    public function run(closure $next, mixed $options = []): void
    {
        $this->configuration->has('mailer.host') and $this->configuration->has('mailer.port')
            or throw new Exception("Configuration: mailer.localhost and mailer.port is required");
        $this->mailer->addMailServer('default', new SmtpSender(
            $this->configuration->get('mailer.host'),
            $this->configuration->get('mailer.port'),
            $this->configuration->get('mailer.protocol', ""),
            $this->configuration->get('mailer.authenticate', false),
            $this->configuration->get('mailer.user', ""),
            $this->configuration->get('mailer.password', "")
        ));
        $this->mailer->useMailSender('default');

        $next();
    }
}