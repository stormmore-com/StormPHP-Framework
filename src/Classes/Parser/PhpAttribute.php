<?php

namespace Stormmore\Framework\Classes\Parser;

class PhpAttribute
{
    public function __construct(public string $name, public string $args)
    {
    }
}