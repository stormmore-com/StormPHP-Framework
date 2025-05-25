<?php

namespace Client;

use PHPUnit\Framework\TestCase;
use Stormmore\Framework\Http\Client;
use Stormmore\Framework\Http\Cookie;
use Stormmore\Framework\Http\Exceptions\HttpClientException;
use Stormmore\Framework\Http\Exceptions\HttpTimeoutException;
use Stormmore\Framework\Http\FormData;
use Stormmore\Framework\Http\Header;
use Stormmore\Framework\Http\Interfaces\IClient;
use Stormmore\Framework\Std\Path;
use Stormmore\Framework\Tests\TestWebServer;

class HttpClientTest extends TestCase
{
    private string $stormFilepath;
    private string $cert;
    private IClient $client;

    public function testGetRequest(): void
    {
        $response = $this->client->request("GET", "/test/get")->send();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("OK", $response->getBody());
    }

    public function testPostRequest(): void
    {
        $response = $this->client->request("POST", "/test/post")->send();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("OK", $response->getBody());
    }

    public function testPutRequest(): void
    {
        $response = $this->client->request("PUT", "/test/put")->send();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("OK", $response->getBody());
    }

    public function testPatchRequest(): void
    {
        $response = $this->client->request("PATCH", "/test/patch")->send();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("OK", $response->getBody());
    }

    public function testDeleteRequest(): void
    {
        $response = $this->client->request("DELETE", "/test/delete")->send();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("OK", $response->getBody());
    }

    public function testGetEndpointWithPost(): void
    {
        $response = $this->client->request("POST", "/test/get")->send();

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testPostEndpointWithGet(): void
    {
        $response = $this->client->request("GET", "/test/post")->send();

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testPutEndpointWithGet(): void
    {
        $response = $this->client->request("GET", "/test/put")->send();

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testPatchEndpointWithGet(): void
    {
        $response = $this->client->request("GET", "/test/patch")->send();

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testDeleteEndpointWithGet(): void
    {
        $response = $this->client->request("GET", "/test/delete")->send();

        $this->assertEquals(404, $response->getStatusCode());
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
                ->addFile('file', $this->stormFilepath))
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

    public function testTimeout(): void
    {
        $client = Client::create("http://localhost:7123", timeout: 1);

        $this->expectException(HttpTimeoutException::class);

        $client->request("GET", "/test/timeout")->send();
    }

    public function testHttpsRequest(): void
    {
        $client = Client::create(cert: $this->cert, timeout: 1);

        $response = $client
            ->request("GET", "https://www.google.com/")
            ->send();

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testImageRequest(): void
    {
        $response = $this->client
            ->request("GET", "/public/image.webp")
            ->send();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('1648c2a85dd50f2dfaa51fb5c8478261', md5($response->getBody()));
    }

    public function setUp(): void
    {
        $filesDirectory = dirname(__FILE__) . "/files" ;
        $this->cert = $filesDirectory . "/cacert.pem";
        $this->stormFilepath = $filesDirectory . "/storm.webp";
        $this->client = Client::create("http://localhost:7123", timeout: 1);
    }
}