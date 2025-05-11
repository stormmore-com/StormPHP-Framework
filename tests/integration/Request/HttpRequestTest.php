<?php

namespace request;

use PHPUnit\Framework\TestCase;
use Stormmore\Framework\Http\Client;
use Stormmore\Framework\Http\FormData;
use Stormmore\Framework\Http\Interfaces\IClient;

class HttpRequestTest extends TestCase
{
    private string $filesDirectory;
    private IClient $client;

    public function testGetRequest(): void
    {
        $response = $this->client->request("GET", "/test/get")->send();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("OK", $response->getBody());
    }

    public function testQueryParameters(): void
    {
        $response = $this->client
            ->request("GET", "/test/concatenate-query-params")
            ->withQuery(['a' => 'one', 'b' => 'two', 'c' => 'three'])
            ->send();

        $this->assertEquals("onetwothree", $response->getBody());
    }

    public function testQueryParametersWithUrl(): void
    {
        $response = $this->client
            ->request("GET", "/test/concatenate-query-params?a=one")
            ->withQuery(['b' => 'two', 'c' => 'three'])
            ->send();

        $this->assertEquals("onetwothree", $response->getBody());
    }

    public function testPostRequest(): void
    {
        $response = $this->client->request("POST", "/test/post")->send();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("OK", $response->getBody());
    }

    public function testPostJson(): void
    {
        $response = $this->client
            ->request("POST", "/test/post/json")
            ->withJson('{"name": "Micheal"}')
            ->send();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('{"name":"Micheal"}', $response->getBody());
    }

    public function tesPostFormWithFiles(): void
    {
        $response = $this->client
            ->request("POST", "/test/post/form")
            ->withForm((new FormData())
                ->add('prime[]', 1)
                ->add('prime[]', 2)
                ->add('number', 7)
                ->add('name', 'Micheal')
                ->addFile('file', $this->filesDirectory . "/storm.webp"))
            ->send();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals((object)[
            'prime' => [1, 2],
            'number' => 7,
            'name' => 'Micheal',
            'file-md5' => '1648c2a85dd50f2dfaa51fb5c8478261'
        ], $response->getJson());
    }

    public function tesPostBody(): void
    {

    }


    public function setUp(): void
    {
        $this->filesDirectory = dirname(__FILE__) . "/files" ;
        $this->client = Client::create("http://localhost:7123");
    }
}