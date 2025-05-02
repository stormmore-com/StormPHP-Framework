<?php

namespace Stormmore\Framework\Mail\Senders;

use Stormmore\Framework\Mail\Mail;

interface IMailSender
{
    function send(Mail $mail): void;
}