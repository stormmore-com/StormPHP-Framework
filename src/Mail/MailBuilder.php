<?php

namespace Stormmore\Framework\Mail;

use Stormmore\Framework\Mail\Senders\IMailSender;
use Stormmore\Framework\Mvc\View\View;

class MailBuilder
{
    private string $recipient;
    private string $subject;
    private string $content;

    public function __construct(private IMailSender $sender)
    {
    }

    public function withRecipient(string $recipient): MailBuilder
    {
        $this->recipient = $recipient;
        return $this;
    }

    public function withSubject(string $subject): MailBuilder
    {
        $this->subject = $subject;
        return $this;
    }

    public function withContent(string $content): MailBuilder
    {
        $this->content = $content;
        return $this;
    }

    public function withContentTemplate(string $template, array $variables = []): MailBuilder
    {
        $view = new View($template, $variables);
        $this->content = $view->toHtml();
        return $this;
    }

    public function send(): void
    {
        $mail = new Mail($this->recipient, $this->subject, $this->content);
        $this->sender->send($mail);
    }
}