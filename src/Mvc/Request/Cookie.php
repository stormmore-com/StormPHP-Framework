<?php

namespace Stormmore\Framework\Mvc\Request;

readonly class Cookie
{
    public function __construct(private string $name, private string $value, private int $expires = 0, private string $path = "/")
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

    public function getExpires(): int
    {
        return $this->expires;
    }

    public function getPath(): string
    {
        return $this->path;
    }
}