<?php

namespace src\App\Params;

use Stormmore\Framework\Mvc\Attributes\Controller;
use Stormmore\Framework\Mvc\Attributes\Route;

#[Controller]
class UntypedController
{
    #[Route("/params/arg")]
    public function required($arg1)
    {
        $len = strlen($arg1);
        return "Param arg1 equals `$arg1` ($len chars)";
    }

    #[Route("/params/arg-default")]
    public function def($arg1 = "default string")
    {
        $len = strlen($arg1);
        return "Param arg1 equals `$arg1` ($len chars)";
    }

    #[Route("/params/arg-optional")]
    public function optional($arg1 = null)
    {
        $len = strlen($arg1);
        return "Param arg1 equals `$arg1` ($len chars)";
    }
}