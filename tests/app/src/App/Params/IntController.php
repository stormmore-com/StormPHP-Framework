<?php

namespace src\App\Params;

use Stormmore\Framework\Mvc\Attributes\Controller;
use Stormmore\Framework\Mvc\Attributes\Route;

#[Controller]
class IntController
{
    #[Route("/params/int")]
    public function required(int $int)
    {
        return "\$int = $int";
    }

    #[Route("/params/int-default")]
    public function def(int $int = 8)
    {
        return "\$int = $int";
    }

    #[Route("/params/int-optional")]
    public function optional(?int $int)
    {
        if ($int == null) {
            return "Int is set to null";
        }
        return "\$int = $int";
    }
}