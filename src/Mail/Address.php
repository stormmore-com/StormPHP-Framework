<?php

namespace Stormmore\Framework\Mail;

class Address
{
    public function __construct(public string $email, public string $name)
    {
    }
}