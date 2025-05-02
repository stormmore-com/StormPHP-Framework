<?php

namespace app\src\Infrastructure;

use Stormmore\Framework\Mail\Mail;
use Stormmore\Framework\Mail\Senders\IMailSender;

class OutputEmailSender implements IMailSender
{
    function send(Mail $mail): void
    {
        echo "Sending email...\n";die;
    }
}