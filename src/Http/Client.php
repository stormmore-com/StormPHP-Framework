<?php

namespace Stormmore\Framework\Http;

class Client implements IClient
{
    public function __construct(private ?string $baseUrl = null)
    {
    }

    public function request(string $method, string $uri): IRequest
    {

    }
}