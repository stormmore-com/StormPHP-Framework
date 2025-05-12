<?php

namespace Stormmore\Framework\Http\Interfaces;

interface IResponse
{
    public function getStatusCode(): int;
    public function getJson(): object;
    public function getBody(): string;
    public function getHeader(string $name): null|IHeader;
    public function getHeaders(): array;
    public function getCookie(string $name): null|ICookie;
}