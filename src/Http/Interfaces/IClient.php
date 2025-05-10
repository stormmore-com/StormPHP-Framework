<?php

namespace Stormmore\Framework\Http\Interfaces;

interface IClient
{
    public function request(string $method, string $url): IRequest;
}