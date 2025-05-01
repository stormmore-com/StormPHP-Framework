<?php

namespace Stormmore\Framework\Cli;

use Attribute;

#[Attribute]
class CliTask
{
    public function __construct(public string $name)
    {
    }
}