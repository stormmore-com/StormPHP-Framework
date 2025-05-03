<?php

namespace Stormmore\Framework\Mail;

use Throwable;
use Stormmore\Framework\App;
use Stormmore\Framework\Internationalization\I18n;
use Stormmore\Framework\Mail\Senders\IMailSender;
use Stormmore\Framework\Mvc\View\View;

class MailBuilder
{
    private string $sender = "";
    private string $recipient = "";
    private string $subject = "";
    private string $content = "";
    /* @var Attachment[] */
    private array $attachments = [];
    private string $contentType = "text/html";
    private string $charset = "utf-8";

    public function __construct(private readonly IMailSender $mailSender)
    {
    }

    public function withSender(string $sender): self
    {
        $this->sender = $sender;
        return $this;
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

    public function withContentTemplate(string $template, array $variables = [], I18n $i18n = null): MailBuilder
    {
        if ($i18n !== null) {
            $container = App::getInstance()->getContainer();
            $requestDefinedI18n = $container->resolve(I18n::class);
            $container->register($i18n);
        }

        try {
            $view = new View($template, $variables);
            $this->content = $view->toHtml();
        }
        catch(Throwable $t) {
            if ($i18n !== null) {
                $container->register($requestDefinedI18n);
            }
            throw $t;
        }

        return $this;
    }

    public function withAttachment(string $filepath, string $name = ""): MailBuilder
    {
        $this->attachments[] = new Attachment($filepath);
        return $this;
    }

    public function withContentType(string $contentType): MailBuilder
    {
        $this->contentType = $contentType;
        return $this;
    }

    public function withCharset(string $charset): MailBuilder
    {
        $this->charset = $charset;
        return $this;
    }

    public function send(): void
    {
        $mail = new Mail($this->sender, $this->recipient, $this->subject, $this->content, $this->attachments, $this->contentType, $this->charset);
        $this->mailSender->send($mail);
    }
}