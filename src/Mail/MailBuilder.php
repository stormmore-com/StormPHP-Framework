<?php

namespace Stormmore\Framework\Mail;

use Exception;
use Throwable;
use Stormmore\Framework\App;
use Stormmore\Framework\Internationalization\I18n;
use Stormmore\Framework\Mail\Senders\IMailSender;
use Stormmore\Framework\Mvc\View\View;

class MailBuilder
{
    private ?Address $sender = null;
    private array $recipients = [];
    private array $cc = [];
    private array $bcc = [];
    private array $replyTo = [];
    private string $subject = "";
    private string $content = "";
    /* @var Attachment[] */
    private array $attachments = [];
    private string $contentType = "text/html";
    private string $charset = "utf-8";

    public function __construct(private readonly IMailSender $mailSender)
    {
    }

    public function withSender(string $sender, string $name = ""): self
    {
        filter_var($sender, FILTER_VALIDATE_EMAIL) or throw new Exception("Invalid sender email");
        $this->sender = new Address($sender, $name);
        return $this;
    }

    public function withRecipient(string $recipient, string $name = ""): MailBuilder
    {
        filter_var($recipient, FILTER_VALIDATE_EMAIL) or throw new Exception("Invalid recipient email");
        $this->recipients[] = new Address($recipient, $name);
        return $this;
    }

    public function withCc(string $recipient, string $name = ""): MailBuilder
    {
        filter_var($recipient, FILTER_VALIDATE_EMAIL) or throw new Exception("Invalid cc email");
        $this->cc[] = new Address($recipient, $name);
        return $this;
    }

    public function withBcc(string $recipient, string $name = ""): MailBuilder
    {
        filter_var($recipient, FILTER_VALIDATE_EMAIL) or throw new Exception("Invalid bcc email");
        $this->bcc[] = new Address($recipient, $name);
        return $this;
    }

    public function withReplyTo(string $recipient, string $name = ""): MailBuilder
    {
        filter_var($recipient, FILTER_VALIDATE_EMAIL) or throw new Exception("Invalid repoly to email");
        $this->replyTo[] = new Address($recipient, $name);
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

    public function withContentTemplate(string $template, array $variables = [], null|I18n $i18n = null): MailBuilder
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
        $this->attachments[] = new Attachment($filepath, $name);
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
        $mail = $this->build();
        $this->mailSender->send($mail);
    }

    public function build(): Mail
    {
        $this->sender !== null or throw new Exception("MailBuilder. Sender is required");
        count($this->recipients) or throw new Exception("MailBuilder. Recipients are required");
        !empty($this->subject) or throw new Exception("MailBuilder. Subject is required");
        !empty($this->content) or throw new Exception("MailBuilder. Content is required");

        return  new Mail(
            $this->sender,
            $this->recipients,
            $this->cc,
            $this->bcc,
            $this->replyTo,
            $this->subject,
            $this->content,
            $this->attachments,
            $this->contentType,
            $this->charset);
    }
}