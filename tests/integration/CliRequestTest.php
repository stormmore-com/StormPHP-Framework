<?php

namespace integration;

use PHPUnit\Framework\TestCase;
use Stormmore\Framework\Tests\AppCookie;
use Stormmore\Framework\Tests\AppRequest;
use Stormmore\Framework\Tests\AppRequestHeader;

class CliRequestTest extends TestCase
{
    private AppRequest $appRequest;

    public function testGetRequest(): void
    {
        $response = $this->appRequest->request("GET", "/test/get")->run();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("OK", $response->getBody());
    }

    public function testPostRequest(): void
    {
        $response = $this->appRequest->request("POST", "/test/post")->run();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("OK", $response->getBody());
    }

    public function testInternalServerError(): void
    {
        $response = $this->appRequest->request("GET", "/test/get500")->run();

        $this->assertEquals(500, $response->getStatusCode());
    }

    public function testQueryParameters(): void
    {
        $response = $this->appRequest->request("GET", "/test/concatenate-query-params?a=one&b=two&c=three")->run();

        $this->assertEquals("onetwothree", $response->getBody());
    }

    public function testReadingHeaderFromRequest(): void
    {
        $response = $this->appRequest->request("GET", "/test/read-header")->run();

        $header  = $response->getHeader("service-key");

        $this->assertEquals("123456790", $header->value);
    }

    public function testSendingHeaderToApp(): void
    {
        $response = $this->appRequest
            ->request("GET", "/test/get-header")
            ->withHeader(AppRequestHeader::create("service-key", "service-key-unique-value"))
            ->run();

        $this->assertEquals("service-key-unique-value", $response->getBody());
    }

    public function testReadingCookieFromRequest(): void
    {
        $response = $this->appRequest
            ->request("GET", "/test/read-cookie")
            ->run();

        $this->assertEquals("0987654321", $response->getCookie('session-id')->value);
    }

    public function testSendingCookieToApp(): void
    {
        $response = $this->appRequest
            ->request("GET", "/test/write-cookie-to-body")
            ->withCookie(AppCookie::create('session-id', 'session-id-unique-value'))
            ->withCookie(AppCookie::create("service-key", "service-key-unique-value"))
            ->run();

        $this->assertEquals("session-id-unique-value", $response->getBody());
    }

    public function testAjax(): void
    {
        $response = $this->appRequest->request("GET", "/test/ajax")->run();

        $json = $response->getJson();

        $this->assertEquals("Micheal", $json->name);
        $this->assertEquals(20, $json->age);
    }

    public function setUp(): void
    {
        $this->appRequest = AppRequest::create("./tests/app/server/index.php");
    }
}