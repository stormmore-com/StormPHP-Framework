<?php

namespace Stormmore\Framework\Tests\Client;

use Exception;
use Stormmore\Framework\Http\Interfaces\IClient;
use Stormmore\Framework\Http\Interfaces\IRequest;

readonly class AppClient implements IClient
{
    private function __construct(private readonly string $indexFilePath)
    {
    }

    public static function create(string $indexFilePath)
    {
        file_exists($indexFilePath) or throw new Exception("Storm app index file not found `$indexFilePath`");
        return new AppClient($indexFilePath);
    }

    public function request(string $method, string $url): IRequest
    {
        $method = strtoupper($method);
        return new AppRequest($this->indexFilePath, $method,$url);
    }
}