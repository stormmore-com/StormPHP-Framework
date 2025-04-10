<?php

namespace Stormmore\Framework\Tests;

class AppRequestHeader
{
    public function __construct(public string $name, public string $value)
    {
    }

    public static function create(string $name, string $value): AppRequestHeader
    {
        return new AppRequestHeader($name, $value);
    }
}