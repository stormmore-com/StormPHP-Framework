<?php

namespace Stormmore\Framework\Tests;

class AppCookie
{
    public function __construct(public string $name, public string $value)
    {
    }

    public static function create(string $name, string $value): AppCookie
    {
        return new AppCookie($name, $value);
    }
}