<?php

namespace Stormmore\Framework\Events;

use Attribute;

#[Attribute]
class EventHandler
{
    public function __construct(public string $className)
    {
    }
}