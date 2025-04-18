<?php

namespace Stormmore\Framework\Http;

interface IClient
{
    public function request(string $method, string $uri): IRequest;
}