<?php

namespace src\App\Params;

use Stormmore\Framework\Mvc\Attributes\Controller;
use Stormmore\Framework\Mvc\Attributes\Route;

#[Controller]
class FloatController
{
    #[Route("/params/float")]
    public function required(float $float)
    {
        return "\$float = $float";
    }

    #[Route("/params/float-default")]
    public function def(float $float = 8.7)
    {
        return "\$float = $float";
    }

    #[Route("/params/float-optional")]
    public function optional(?float $float)
    {
        if ($float == null) {
            return "Float is set to null";
        }
        return "\$float = $float";
    }
}