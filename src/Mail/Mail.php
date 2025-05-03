<?php

namespace Stormmore\Framework\Mail;

class Mail
{
    public function __construct(
        public string $sender,
        public string $recipient,
        public string $subject,
        public string $content,
        /** @var Attachment[] */
        public array $attachments = [],
        public string $contentType = "text/html",
        public string $charset = "utf-8")
    {
    }
}