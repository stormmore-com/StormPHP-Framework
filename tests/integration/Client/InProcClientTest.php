<?php

namespace Client;

use PHPUnit\Framework\TestCase;
use Stormmore\Framework\Http\Cookie;
use Stormmore\Framework\Http\FormData;
use Stormmore\Framework\Http\Header;
use Stormmore\Framework\Tests\Client\AppClient;

class InProcClientTest extends TestCase
{
    private string $stormFilepath;
    private string $filesDirectory;
    private AppClient $client;

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
            ->withQuery(['a' => '11', 'b' => '22', 'c' => '33'])
            ->send();

        $this->assertEquals("112233", $response->getBody());
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

    public function testPostFormWithFiles(): void
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

    public function testPostBody(): void
    {
        $response = $this->client
            ->request("POST", "/test/post/file-in-body")
            ->withContent(file_get_contents($this->stormFilepath))
            ->send();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("1648c2a85dd50f2dfaa51fb5c8478261", $response->getBody());
    }

    public function testInternalServerError(): void
    {
        $response = $this->client->request("GET", "/test/get500")->send();

        $this->assertEquals(500, $response->getStatusCode());
    }

    public function testReadingHeaderFromRequest(): void
    {
        $response = $this->client->request("GET", "/test/read-header")->send();

        $header  = $response->getHeader("service-key");

        $this->assertEquals("123456790", $header->getValue());
    }

    public function testSendingHeader(): void
    {
        $response = $this->client
            ->request("GET", "/test/get-header")
            ->withHeader(new Header("service-key", "service-key-unique-value"))
            ->send();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("service-key-unique-value", $response->getBody());
    }

    public function testReadingCookieFromRequest(): void
    {
        $response = $this->client
            ->request("GET", "/test/read-cookie")
            ->send();

        $this->assertEquals("0987654321", $response->getCookie('session-id')->getValue());
    }

    public function testSendingCookie(): void
    {
        $response = $this->client
            ->request("GET", "/test/write-cookie-to-body")
            ->withCookie(new Cookie('session-id', 'session-id-unique-value'))
            ->withCookie(new Cookie("service-key", "service-key-unique-value"))
            ->send();

        $this->assertEquals("session-id-unique-value", $response->getBody());
    }

    public function testAjax(): void
    {
        $response = $this->client->request("GET", "/test/ajax")->send();

        $json = $response->getJson();

        $this->assertEquals("Micheal", $json->name);
        $this->assertEquals(20, $json->age);
    }

    public function setUp(): void
    {
        $this->filesDirectory = dirname(__FILE__) . "/files" ;
        $this->stormFilepath = $this->filesDirectory . "/storm.webp";
        $this->client = AppClient::create("app/public_html/index.php");
    }
}