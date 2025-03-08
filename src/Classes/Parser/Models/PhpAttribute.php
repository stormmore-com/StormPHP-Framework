<?php

namespace Stormmore\Framework\Classes\Parser\Models;

class PhpAttribute
{
    public function __construct(public string $name, public string $args)
    {
    }
}