<?php

namespace Stormmore\Framework\Http;

use Stormmore\Framework\Http\Interfaces\ICookie;
use Stormmore\Framework\Http\Interfaces\IHeader;
use Stormmore\Framework\Http\Interfaces\IResponse;

readonly class Response implements IResponse
{
    public function __construct(private string $body,
                                private int $status = 200,
                                private array $headers = [],
                                private array $cookies = [])
    {
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

    public function getCookie(string $name): null|ICookie
    {
        if (array_key_exists($name, $this->cookies)) {
            return new Cookie($name, $this->cookies[$name]);
        }
        return null;
    }
}