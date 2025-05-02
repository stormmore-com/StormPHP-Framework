<?php

namespace Stormmore\Framework\Mail\Senders;

use Stormmore\Framework\Mail\Mail;

class PhpMail implements IMailSender
{
    public function send(Mail $mail): void
    {
        mail($mail->recipient, $mail->subject, $mail->content);
    }
}