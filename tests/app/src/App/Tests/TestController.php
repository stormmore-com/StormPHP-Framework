<?php

namespace app\src\App\Tests;

use Exception;
use stdClass;
use Stormmore\Framework\Mvc\Attributes\Controller;
use Stormmore\Framework\Mvc\Attributes\Get;
use Stormmore\Framework\Mvc\Attributes\Post;
use Stormmore\Framework\Mvc\Attributes\Route;
use Stormmore\Framework\Mvc\IO\Request\Request;
use Stormmore\Framework\Mvc\IO\Cookie\Cookie;
use Stormmore\Framework\Mvc\IO\Response;

#[Controller]
class TestController
{
    public function __construct(private Request $request, private Response $response)
    {
    }

    #[Get]
    #[Route("/test/get")]
    public function get(): string
    {
        return "OK";
    }

    #[Post]
    #[Route("/test/post")]
    public function post(): string
    {
        return "OK";
    }

    #[Route("/test/get500")]
    public function get500(): string
    {
        throw new Exception();
    }

    #[Route("/test/concatenate-query-params")]
    public function concatenate(string $a, string $b, string $c): string
    {
        return $a . $b . $c;
    }
    #[Route("/test/read-header")]
    public function readHeader(): string
    {
        $this->response->addHeader("service-key", "123456790");
        return "";
    }

    #[Route("/test/get-header")]
    public function getHeader(): string
    {
        return $this->request->getHeader("service-key")->value;
    }

    #[Route("/test/read-cookie")]
    public function readCookie(): string
    {
        $this->response->cookies->add(new Cookie("session-id", "0987654321"));
        $this->response->cookies->add(new Cookie("locale", "en-US"));
        return "";
    }

    #[Route("/test/write-cookie-to-body")]
    public function writeCookieToBody(): string
    {
        return $this->request->cookies->get("session-id")->getValue();
    }

    #[Route("/test/ajax")]
    public function ajax(): object
    {
        $object = new stdClass();
        $object->name = "Micheal";
        $object->age = 20;
        return $object;
    }
}