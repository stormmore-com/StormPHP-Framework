<?php

namespace Stormmore\Framework\Mail;

class Mail
{
    public function __construct(
        public string $recipient,
        public string $subject,
        public string $content)
    {
    }
}