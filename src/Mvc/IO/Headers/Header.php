<?php

namespace Stormmore\Framework\Mvc\IO\Headers;

class Header
{
    public function __construct(public string $name, public string $value = '')
    {
    }
}