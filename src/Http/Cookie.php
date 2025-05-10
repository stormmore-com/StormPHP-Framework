<?php

namespace Stormmore\Framework\Http;

use Stormmore\Framework\Http\Interfaces\ICookie;

readonly class Cookie implements ICookie
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