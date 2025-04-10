<?php

namespace Stormmore\Framework\Mvc\IO\Request;

class Header
{
    public function __construct(public string $name, public string $value = '')
    {
    }
}