<?php

namespace Stormmore\Framework\Mail;

class Mail
{
    public function __construct(
        public Address $sender,
        /** @var Address[] */
        public array $recipients,
        public array $cc,
        public array $bcc,
        public array $replyTo,
        public string $subject,
        public string $content,
        /** @var Attachment[] */
        public array $attachments = [],
        public string $contentType = "text/html",
        public string $charset = "utf-8")
    {
    }
}