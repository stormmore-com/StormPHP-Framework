<?php

namespace integration;

use PHPUnit\Framework\TestCase;
use Stormmore\Framework\Tests\AppRequest;

class CliRequestTest extends TestCase
{
    private AppRequest $appRequest;

    public function testRequestGet(): void
    {
        $response = $this->appRequest->setUrl("/test/get")->run();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("OK", $response->getBody());
    }

    public function testInternalServerError(): void
    {
        $response = $this->appRequest->setUrl("/test/get500")->run();

        $this->assertEquals(500, $response->getStatusCode());
    }

    public function testQueryParameters(): void
    {
        $response = $this->appRequest->setUrl("/test/concatenate?a=one&b=two&c=three")->run();

        $this->assertEquals("onetwothree", $response->getBody());
    }

    public function testAjax(): void
    {
        $response = $this->appRequest->setUrl("/test/ajax")->run();

        $json = $response->getJson();

        $this->assertEquals("Micheal", $json->name);
        $this->assertEquals(20, $json->age);
    }

    public function setUp(): void
    {
        $this->appRequest = AppRequest::create("./tests/app/server/index.php");
    }
}