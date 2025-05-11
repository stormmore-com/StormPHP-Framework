<?php

namespace Stormmore\Framework\Mvc\IO\Headers;

use Stormmore\Framework\Http\Interfaces\IHeader;

class Headers
{
    public function __construct(private array $headers)
    {
    }

    public function has(string $name): bool
    {
        return array_key_exists($name, $this->headers);
    }

    public function get(string $name): null|IHeader
    {
        return $this->headers[$name];
    }
}