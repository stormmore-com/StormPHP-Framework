<?php

namespace Stormmore\Framework\FluentReflection\Shared;

class SafeValue
{
    public function __construct(public bool $exist = false, public mixed $value = null)
    {
    }
}