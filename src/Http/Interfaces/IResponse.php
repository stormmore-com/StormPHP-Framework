<?php

namespace Stormmore\Framework\Http\Interfaces;

interface IResponse
{
    public function getStatusCode(): int;
    public function getJson(): object;
    public function getBody(): string;
    public function getHeader(string $name): ?IHeader;
    public function getHeaders(): array;
}