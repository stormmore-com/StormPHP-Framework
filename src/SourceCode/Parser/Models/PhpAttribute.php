<?php

namespace Stormmore\Framework\SourceCode\Parser\Models;

class PhpAttribute
{
    public function __construct(public string $name, public string $args)
    {
    }
}