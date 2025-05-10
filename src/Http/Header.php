<?php

namespace Stormmore\Framework\Http;

use Stormmore\Framework\Http\Interfaces\IHeader;

readonly class Header implements IHeader
{
    public function __construct(private string $name, private string $value)
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