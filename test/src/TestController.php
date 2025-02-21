<?php

namespace src;

use Stormmore\Framework\Mvc\Controller;
use Stormmore\Framework\Mvc\Route;
use Stormmore\Framework\Mvc\View;

#[Controller]
class TestController
{
    #[Route("/test")]
    public function index(): View
    {
        return view("@src/test_template.php");
    }
}