<?php

namespace Stormmore\Framework\Http;

use Stormmore\Framework\Http\Interfaces\IHeader;
use Stormmore\Framework\Http\Interfaces\IResponse;

class Response implements IResponse
{

    public function __construct(private string $body, private int $status = 200)
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

    public function getHeader(string $name): ?IHeader
    {
        // TODO: Implement getHeader() method.
    }

    public function getHeaders(): array
    {
        // TODO: Implement getHeaders() method.
    }
}