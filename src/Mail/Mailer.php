<?php

namespace Stormmore\Framework\Mail;

use Stormmore\Framework\Mail\Senders\IMailSender;
use Stormmore\Framework\Mail\Senders\PhpMail;
use Stormmore\Framework\Mail\Senders\SmtpSender;

class Mailer
{
    private string $defaultSender = 'php-mail';

    /**
     * @var IMailSender[]
     */
    private array $senders = [];

    public function __construct()
    {
        $this->senders['php-mail'] = new PhpMail();
    }

    public function create(null|string $recipient = null, ?string $subject = null, ?string $content = null): MailBuilder
    {
        $builder = new MailBuilder($this->senders[$this->defaultSender]);
        if (is_string($recipient)) {
            $builder->withRecipient($recipient);
        }
        if ($subject) {
            $builder->withSubject($subject);
        }
        if ($content) {
            $builder->withContent($content);
        }
        return $builder;
    }

    public function useMailSender(string $sender): Mailer
    {
        if (array_key_exists($sender, $this->senders)) {
            $this->defaultSender = $sender;
        }
        return $this;
    }

    public function addMailServer(string $name, IMailSender $sender): void
    {
        $this->senders[$name] = $sender;
    }
}