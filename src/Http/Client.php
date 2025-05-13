<?php

namespace Stormmore\Framework\Http;

use Stormmore\Framework\Http\Interfaces\IClient;
use Stormmore\Framework\Http\Interfaces\IRequest;

class Client implements IClient
{
    public function __construct(private ?string $baseUrl = null,
                                private bool $verifySslPeer = true,
                                private string $cert = "",
                                private int $timeout = 0)
    {
    }

    public static function create(?string $baseUrl = null,
                                  bool $verifySslPeer = true,
                                  string $cert = "",
                                  int $timeout = 0): Client
    {
        return new Client($baseUrl, $verifySslPeer, $cert, $timeout);
    }

    public function request(string $method, string $url): IRequest
    {
        if ($this->baseUrl) {
            $url = $this->baseUrl . $url;
        }
        return new Request($url, $method, $this->verifySslPeer, $this->cert, $this->timeout);
    }
}