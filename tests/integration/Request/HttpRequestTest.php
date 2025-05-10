<?php

namespace request;

use PHPUnit\Framework\TestCase;
use Stormmore\Framework\Http\Client;
use Stormmore\Framework\Http\Interfaces\IClient;

class HttpRequestTest extends TestCase
{
    private IClient $client;

    public function testGetRequest(): void
    {
        $response = $this->client->request("GET", "/test/get")->send();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("OK", $response->getBody());
    }

    public function setUp(): void
    {
        $this->client = Client::create("http://localhost:7123");
    }
}