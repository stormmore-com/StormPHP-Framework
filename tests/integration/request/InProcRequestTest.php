<?php

namespace integration\request;

use PHPUnit\Framework\TestCase;
use Stormmore\Framework\Http\Cookie;
use Stormmore\Framework\Http\FormData;
use Stormmore\Framework\Http\Header;
use Stormmore\Framework\Tests\AppClient;

class InProcRequestTest extends TestCase
{
    private AppClient $appClient;

    public function testGetRequest(): void
    {
        $response = $this->appClient->request("GET", "/test/get")->call();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("OK", $response->getBody());
    }

    public function testQueryParameters(): void
    {
        $response = $this->appClient
            ->request("GET", "/test/concatenate-query-params")
            ->withQuery(['a' => 'one', 'b' => 'two', 'c' => 'three'])
            ->call();

        $this->assertEquals("onetwothree", $response->getBody());
    }

    public function testQueryParametersWithUrl(): void
    {
        $response = $this->appClient
            ->request("GET", "/test/concatenate-query-params?a=one")
            ->withQuery(['b' => 'two', 'c' => 'three'])
            ->call();

        $this->assertEquals("onetwothree", $response->getBody());
    }

    public function testPostRequest(): void
    {
        $response = $this->appClient->request("POST", "/test/post")->call();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("OK", $response->getBody());
    }

    public function testPostJson(): void
    {
        $response = $this->appClient
            ->request("POST", "/test/post/json")
            ->withJson('{"name": "Micheal"}')
            ->call();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('{"name":"Micheal"}', $response->getBody());
    }

    public function testPostFormWithFiles(): void
    {
        /*
         *
         * [
                'arr[]' => 5,
                'number' => 7,
                'name' => 'Micheal',
                'file' => new AppRequestFile(''),
                ]
         */
        $response = $this->appClient
            ->request("POST", "/test/post/form")
            ->withForm((new FormData())
                ->add('arr[]', 5)
                ->add('number', 7)
                ->add('name', 'Micheal')
                ->addFile('file',''))
            ->call();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('{"name":"Micheal"}', $response->getBody());
    }
//
//    public function testPostBinary(): void
//    {
//
//    }
//
//    public function testPostBody(): void
//    {
//
//    }


    public function testInternalServerError(): void
    {
        $response = $this->appClient->request("GET", "/test/get500")->call();

        $this->assertEquals(500, $response->getStatusCode());
    }


    public function testReadingHeaderFromRequest(): void
    {
        $response = $this->appClient->request("GET", "/test/read-header")->call();

        $header  = $response->getHeader("service-key");

        $this->assertEquals("123456790", $header->getValue());
    }

    public function testSendingHeaderToApp(): void
    {
        $response = $this->appClient
            ->request("GET", "/test/get-header")
            ->withHeader(new Header("service-key", "service-key-unique-value"))
            ->call();

        $this->assertEquals("service-key-unique-value", $response->getBody());
    }

    public function testReadingCookieFromRequest(): void
    {
        $response = $this->appClient
            ->request("GET", "/test/read-cookie")
            ->call();

        $this->assertEquals("0987654321", $response->getCookie('session-id')->getValue());
    }

    public function testSendingCookieToApp(): void
    {
        $response = $this->appClient
            ->request("GET", "/test/write-cookie-to-body")
            ->withCookie(new Cookie('session-id', 'session-id-unique-value'))
            ->withCookie(new Cookie("service-key", "service-key-unique-value"))
            ->call();

        $this->assertEquals("session-id-unique-value", $response->getBody());
    }

    public function testAjax(): void
    {
        $response = $this->appClient->request("GET", "/test/ajax")->call();

        $json = $response->getJson();

        $this->assertEquals("Micheal", $json->name);
        $this->assertEquals(20, $json->age);
    }

    public function setUp(): void
    {
        $this->appClient = AppClient::create("./tests/app/server/index.php");
    }
}