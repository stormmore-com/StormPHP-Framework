<?php

namespace src\App\Params;


use Stormmore\Framework\Mvc\Attributes\Controller;
use Stormmore\Framework\Mvc\Attributes\Route;

#[Controller]
class BoolController
{
    #[Route("/params/bool")]
    public function required(bool $bool)
    {
        if ($bool) {
            return "Param is set to true";
        }
        return "Param is set to false";

    }

    #[Route("/params/bool-default")]
    public function def(bool $bool = true)
    {
        if ($bool) {
            return "Param is set to true";
        }
        return "Param is set to false";
    }

    #[Route("/params/bool-optional")]
    public function optional(?bool $bool)
    {
        if ($bool == null) {
            return "Param is set to null";
        }
        else if ($bool) {
            return "Param is set to true";
        }
        else {
            return "Param is set to false";
        }
    }
}