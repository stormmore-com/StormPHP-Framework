<?php

namespace app\src\App\Tests;

use Exception;
use stdClass;
use Stormmore\Framework\Mvc\Attributes\Controller;
use Stormmore\Framework\Mvc\Attributes\Route;

#[Controller]
class TestController
{
    #[Route("/test/get")]
    public function get(): string
    {
        return "OK";
    }

    #[Route("/test/get500")]
    public function get500(): string
    {
        throw new Exception();
    }

    #[Route("/test/concatenate")]
    public function concatenate(string $a, string $b, string $c): string
    {
        return $a . $b . $c;
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