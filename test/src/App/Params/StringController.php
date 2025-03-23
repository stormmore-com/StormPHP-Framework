<?php

namespace Params;

use Stormmore\Framework\Mvc\Attributes\Controller;
use Stormmore\Framework\Mvc\Attributes\Route;

#[Controller]
class StringController
{
    #[Route("/params/string")]
    public function required(string $string)
    {
        $count = strlen($string);
        return "Param string equals '$string' ({$count} chars)";
    }

    #[Route("/params/string-default")]
    public function def(string $string = "default string")
    {
        $count = strlen($string);
        return "Param string equals '$string' ({$count} chars)";
    }

    #[Route("/params/string-optional")]
    public function optional(?string $string)
    {
        $count = strlen($string);
        return "Param string equals '$string' ({$count} chars)";
    }
}