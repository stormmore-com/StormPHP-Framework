<?php

namespace Stormmore\Framework\Cqs;

use Attribute;

#[Attribute]
class CommandHandler
{
    public function __construct(public string $className)
    {
    }
}