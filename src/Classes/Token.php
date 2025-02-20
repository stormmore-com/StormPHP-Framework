<?php

namespace Stormmore\Framework\Classes;

readonly class Token
{
    public function __construct(public string $name, public string $value)
    {
    }
}