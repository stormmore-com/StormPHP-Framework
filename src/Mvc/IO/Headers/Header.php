<?php

namespace Stormmore\Framework\Mvc\IO\Headers;

use Stormmore\Framework\Http\Interfaces\IHeader;

class Header implements IHeader
{
    public function __construct(private string $name, private string $value = '')
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}