<?php

namespace Stormmore\Framework\Http;

use Stormmore\Framework\Http\Interfaces\IHeader;
use Stormmore\Framework\Http\Interfaces\IResponse;
use Stormmore\Framework\Mvc\IO\Headers\Headers;

class Response implements IResponse
{
    private array $headers;

    public function __construct(private string $body, private int $status = 200, array $headers = [])
    {
        $this->headers = $headers;
    }

    public function getStatusCode(): int
    {
        return $this->status;
    }

    public function getJson(): object
    {
        return json_decode($this->body);
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getHeader(string $name): null|IHeader
    {
        if (array_key_exists($name, $this->headers)) {
            return new Header($name, $this->headers[$name]);
        }
        return null;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }
}