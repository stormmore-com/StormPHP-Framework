<?php

namespace Stormmore\Framework\SourceCode;

readonly class Token
{
    public function __construct(public string $name, public string $value)
    {
    }
}