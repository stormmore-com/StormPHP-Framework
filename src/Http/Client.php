<?php

namespace Stormmore\Framework\Http;

use Stormmore\Framework\Http\Interfaces\IClient;
use Stormmore\Framework\Http\Interfaces\IRequest;

class Client implements IClient
{
    public function __construct(private ?string $baseUrl = null)
    {
    }

    public static function create(?string $baseUrl = null): Client
    {
        return new Client($baseUrl);
    }

    public function request(string $method, string $url): IRequest
    {
        if ($this->baseUrl) {
            $url = $this->baseUrl . $url;
        }
        return new Request($url, $method);
    }
}